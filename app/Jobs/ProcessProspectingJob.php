<?php

namespace App\Jobs;

use App\Models\AppSetting;
use App\Models\Prospect;
use App\Models\QueuePause;
use App\Models\User;
use App\Models\UserSearch;
use App\Services\CityNormalizationService;
use App\Services\GoogleMapsScraperService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProcessProspectingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $userId,
        public string $cidade,
        public string $nicho,
        public ?int $maxResults = null
    ) {
        $this->onQueue('prospecting');
    }

    /**
     * Execute the job.
     */
    public function handle(
        GoogleMapsScraperService $scraper,
        CityNormalizationService $cityNormalizer
    ): void {
        // Verifica se a fila está pausada (global ou específica)
        if (QueuePause::isQueuePaused(null) || QueuePause::isQueuePaused('prospecting')) {
            Log::info('Prospecting job skipped - queue is paused', [
                'user_id' => $this->userId,
                'cidade' => $this->cidade,
                'nicho' => $this->nicho,
            ]);
            
            // Rejoga o job para a fila para tentar novamente depois
            $this->release(60); // Tenta novamente em 60 segundos
            return;
        }

        // Normaliza a cidade ANTES de verificar duplicatas
        $normalizedCity = $cityNormalizer->normalizeCity($this->cidade);
        
        if (!$normalizedCity) {
            Log::warning('City normalization failed', ['cidade' => $this->cidade]);
            return;
        }

        // Verifica se já existe pesquisa similar COMPLETA (reutilização)
        $existingSearch = UserSearch::where('user_id', $this->userId)
            ->where(function ($query) use ($normalizedCity) {
                $query->where('normalized_cidade', $normalizedCity)
                      ->orWhere('cidade', $this->cidade);
            })
            ->where('nicho', $this->nicho)
            ->where('status', 'completed')
            ->whereNotNull('raw_data')
            ->orderBy('created_at', 'desc')
            ->first();

        // Se existe pesquisa anterior com dados, reutiliza
        if ($existingSearch && !empty($existingSearch->raw_data)) {
            Log::info('Reusing existing search', [
                'user_id' => $this->userId,
                'cidade' => $this->cidade,
                'nicho' => $this->nicho,
                'existing_search_id' => $existingSearch->id,
            ]);

            // Cria nova entrada de pesquisa referenciando a anterior
            $userSearch = UserSearch::create([
                'user_id' => $this->userId,
                'cidade' => $this->cidade,
                'normalized_cidade' => $normalizedCity,
                'nicho' => $this->nicho,
                'status' => 'pending',
                'raw_data' => $existingSearch->raw_data, // Reutiliza dados
            ]);

            // Limita os dados se maxResults foi especificado
            $businessesToProcess = $existingSearch->raw_data;
            if ($this->maxResults !== null && count($businessesToProcess) > $this->maxResults) {
                $businessesToProcess = array_slice($businessesToProcess, 0, $this->maxResults);
            }

            // Processa os dados reutilizados
            $this->processBusinesses($businessesToProcess, $userSearch, $scraper);
            return;
        }

        // Registra nova pesquisa
        $userSearch = UserSearch::create([
            'user_id' => $this->userId,
            'cidade' => $this->cidade,
            'normalized_cidade' => $normalizedCity,
            'nicho' => $this->nicho,
            'status' => 'pending',
        ]);

        try {
            Log::info('Starting prospecting job', [
                'user_id' => $this->userId,
                'cidade' => $this->cidade,
                'normalized_cidade' => $normalizedCity,
                'nicho' => $this->nicho,
            ]);

            // Verifica cota ANTES de chamar API do Maps
            $quotaCheck = $this->checkQuotaExceeded();
            if ($quotaCheck['exceeded']) {
                $userSearch->update([
                    'status' => 'failed',
                    'completed_at' => now(),
                ]);

                Log::warning('Quota exceeded before processing', [
                    'user_id' => $this->userId,
                    'message' => $quotaCheck['message'],
                ]);

                return;
            }

            // Busca empresas no Google Maps (usa cidade normalizada internamente)
            // O searchBusinesses já verifica banco de dados e cache ANTES de chamar API
            $businesses = $scraper->searchBusinesses($this->cidade, $this->nicho, $this->userId, $this->maxResults);

            if (empty($businesses)) {
                $userSearch->update([
                    'status' => 'failed',
                    'results_count' => 0,
                    'completed_at' => now(),
                ]);

                Log::warning('No businesses found', [
                    'user_id' => $this->userId,
                    'cidade' => $this->cidade,
                    'nicho' => $this->nicho,
                ]);
                return;
            }

            // Salva dados brutos da pesquisa (apenas se não vieram do banco)
            // Se vieram do banco, já estão salvos, mas atualizamos esta pesquisa também
            $userSearch->update([
                'raw_data' => $businesses,
            ]);

            // Processa os negócios
            $this->processBusinesses($businesses, $userSearch, $scraper);

        } catch (\Exception $e) {
            $userSearch->update([
                'status' => 'failed',
                'completed_at' => now(),
            ]);

            Log::error('Fatal error in prospecting job', [
                'user_id' => $this->userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Processa lista de negócios
     */
    private function processBusinesses(array $businesses, UserSearch $userSearch, GoogleMapsScraperService $scraper): void
    {
        $processed = 0;
        $errors = 0;
        $quotaExceeded = false;

        foreach ($businesses as $business) {
            try {
                // Verifica cota ANTES de criar cada prospect
                $quotaCheck = $this->checkQuotaExceeded();
                if ($quotaCheck['exceeded']) {
                    $quotaExceeded = true;
                    Log::warning('Quota exceeded during processing', [
                        'user_id' => $this->userId,
                        'processed' => $processed,
                        'message' => $quotaCheck['message'],
                    ]);
                    break; // Para de processar quando atingir a cota
                }

                // Verifica se já existe (evita duplicatas) - usa cidade original para busca
                $existing = Prospect::where('user_id', $this->userId)
                    ->where('nome', $business['nome'])
                    ->where(function ($query) use ($userSearch) {
                        $query->where('cidade', $this->cidade)
                              ->orWhere('cidade', $userSearch->normalized_cidade ?? $this->cidade);
                    })
                    ->where('nicho', $this->nicho)
                    ->first();

                if ($existing) {
                    // Atualiza dados se necessário (reutiliza prospect existente)
                    $processed++;
                    continue;
                }

                // Obtém telefone e site via Place Details (com cache)
                $phone = null;
                $website = null;
                
                if (!empty($business['place_id'])) {
                    // Verifica cota novamente antes de chamar API
                    $quotaCheck = $this->checkQuotaExceeded();
                    if ($quotaCheck['exceeded']) {
                        $quotaExceeded = true;
                        Log::warning('Quota exceeded before Place Details API call', [
                            'user_id' => $this->userId,
                            'processed' => $processed,
                        ]);
                        break;
                    }

                    $placeDetails = $scraper->getPlaceDetails($business['place_id'], $this->userId);
                    if ($placeDetails) {
                        $phone = $placeDetails['telefone'] ?? null;
                        $website = $placeDetails['site'] ?? null;
                    }
                }

                // Cria o prospect inicial
                $prospect = Prospect::create([
                    'user_id' => $this->userId,
                    'nome' => $business['nome'],
                    'cidade' => $this->cidade, // Salva cidade original
                    'nicho' => $this->nicho,
                    'google_maps_url' => $business['google_maps_url'] ?? null,
                    'endereco' => $business['endereco'] ?? null,
                    'telefone' => $phone,
                    'site' => $website,
                    'status' => 'pending',
                ]);

                // Se houver site, busca informações adicionais (email, WhatsApp)
                if (!empty($website)) {
                    $websiteData = $scraper->scrapeWebsite($website);
                    
                    $prospect->update([
                        'email' => $websiteData['email'],
                        'whatsapp' => $websiteData['whatsapp'],
                        // Mantém telefone da API se não tiver do site
                        'telefone' => $websiteData['telefone'] ?? $phone,
                        'status' => 'done',
                    ]);
                } else {
                    // Se não tem site mas tem telefone, marca como done
                    $prospect->update(['status' => 'done']);
                }

                $processed++;

                // Rate limiting: aguarda 1 segundo entre processamentos
                usleep(1000000); // 1 segundo

            } catch (\Exception $e) {
                $errors++;
                Log::error('Error processing business', [
                    'business' => $business,
                    'error' => $e->getMessage(),
                ]);

                // Marca como erro se o prospect foi criado
                if (isset($prospect)) {
                    $prospect->update(['status' => 'error']);
                }
            }
        }

        // Se a cota foi excedida, atualiza o status da pesquisa
        if ($quotaExceeded) {
            $userSearch->update([
                'status' => 'failed',
                'results_count' => $processed,
                'completed_at' => now(),
            ]);

            Log::info('Processing stopped due to quota exceeded', [
                'user_id' => $this->userId,
                'processed' => $processed,
                'errors' => $errors,
            ]);
            return;
        }

        // Atualiza a pesquisa como concluída
        $userSearch->update([
            'status' => 'completed',
            'results_count' => $processed,
            'completed_at' => now(),
        ]);

        Log::info('Prospecting job completed', [
            'user_id' => $this->userId,
            'processed' => $processed,
            'errors' => $errors,
        ]);
    }

    /**
     * Verifica se a cota foi excedida
     */
    private function checkQuotaExceeded(): array
    {
        $user = User::with('plan')->find($this->userId);
        if (!$user) {
            return ['exceeded' => false, 'message' => ''];
        }

        // Recarrega o usuário para garantir valores atualizados (incluindo quotas customizadas)
        $user->refresh();

        // Usa os métodos do User que já verificam quotas customizadas
        $monthlyQuota = $user->getEffectiveMonthlyQuota();
        $dailyQuota = $user->getEffectiveDailyQuota();

        $today = now()->startOfDay();
        $monthStart = now()->startOfMonth();

        $dailyCount = Prospect::forUser($this->userId)
            ->where('created_at', '>=', $today)
            ->count();

        $monthlyCount = Prospect::forUser($this->userId)
            ->where('created_at', '>=', $monthStart)
            ->count();

        $dailyExceeded = $dailyQuota > 0 && $dailyCount >= $dailyQuota;
        $monthlyExceeded = $monthlyQuota > 0 && $monthlyCount >= $monthlyQuota;

        if ($dailyExceeded && $monthlyExceeded) {
            return [
                'exceeded' => true,
                'message' => "Limite diário ({$dailyCount}/{$dailyQuota}) e mensal ({$monthlyCount}/{$monthlyQuota}) atingido.",
                'type' => 'both'
            ];
        }

        if ($dailyExceeded) {
            return [
                'exceeded' => true,
                'message' => "Limite diário atingido ({$dailyCount}/{$dailyQuota}).",
                'type' => 'daily'
            ];
        }

        if ($monthlyExceeded) {
            return [
                'exceeded' => true,
                'message' => "Limite mensal atingido ({$monthlyCount}/{$monthlyQuota}).",
                'type' => 'monthly'
            ];
        }

        return [
            'exceeded' => false,
            'message' => '',
            'type' => null
        ];
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Prospecting job failed', [
            'user_id' => $this->userId,
            'cidade' => $this->cidade,
            'nicho' => $this->nicho,
            'error' => $exception->getMessage(),
        ]);
    }
}
