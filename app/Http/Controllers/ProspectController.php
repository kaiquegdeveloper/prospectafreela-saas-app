<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessProspectingJob;
use App\Models\AppSetting;
use App\Models\ContactMessageTemplate;
use App\Models\Prospect;
use App\Models\ProspectLead;
use App\Models\UserSearch;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProspectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $user = Auth::user();

        $query = Prospect::forUser($userId)
            ->with('lead')
            ->orderBy('created_at', 'desc');

        // Busca
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filtro por status
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Filtro por nicho
        if ($request->filled('nicho')) {
            $query->where('nicho', $request->nicho);
        }

        $prospects = $query->paginate(15)->withQueryString();

        // Nichos disponíveis para filtro
        $niches = Prospect::forUser($userId)
            ->select('nicho')
            ->distinct()
            ->orderBy('nicho')
            ->pluck('nicho');

        // Uso de cota (diária e mensal)
        $usage = $this->getUsageData($userId);

        // Verifica se a cota foi excedida
        $quotaCheck = $this->checkQuotaExceeded($userId);

        // Mensagem padrão para WhatsApp
        $whatsappMessage = ContactMessageTemplate::where('channel', 'whatsapp')
            ->where('is_active', true)
            ->orderBy('id')
            ->value('content');

        return view('prospects.index', [
            'prospects' => $prospects,
            'niches' => $niches,
            'usage' => $usage,
            'whatsappMessage' => $whatsappMessage,
            'quotaData' => $quotaCheck,
            'user' => $user,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $userId = Auth::id();
        $user = Auth::user();
        $user->refresh(); // Garante dados atualizados

        $usage = $this->getUsageData($userId);
        $maxApiFetches = $user->getEffectiveMaxApiFetches();

        // Verifica se a cota foi excedida
        $quotaCheck = $this->checkQuotaExceeded($userId);

        // Busca sugestões de cidades das últimas pesquisas
        $suggestedCities = UserSearch::where('user_id', $userId)
            ->whereNotNull('cidade')
            ->select('cidade', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('cidade')
            ->take(10)
            ->pluck('cidade')
            ->toArray();

        // Busca sugestões de nichos das últimas pesquisas
        $suggestedNiches = UserSearch::where('user_id', $userId)
            ->whereNotNull('nicho')
            ->select('nicho', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('nicho')
            ->take(10)
            ->pluck('nicho')
            ->toArray();

        // Busca serviços/categorias de scripts de vendas
        $services = \App\Models\SalesScriptCategory::where('is_active', true)
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        return view('prospects.create', [
            'usage' => $usage,
            'maxApiFetches' => $maxApiFetches,
            'suggestedCities' => $suggestedCities,
            'suggestedNiches' => $suggestedNiches,
            'services' => $services,
            'quotaData' => $quotaCheck,
            'user' => $user,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $userId = Auth::id();
        $user = Auth::user();
        $user->refresh(); // Garante dados atualizados
        $maxApiFetches = $user->getEffectiveMaxApiFetches();

        $validated = $request->validate([
            'cidade' => ['required', 'string', 'max:255'],
            'nicho' => ['required', 'string', 'max:255'],
            'servico' => ['nullable', 'string', 'max:255'],
            'max_results' => ['nullable', 'integer', 'min:1', "max:{$maxApiFetches}"],
            'only_valid_email' => ['nullable', 'boolean'],
            'only_valid_site' => ['nullable', 'boolean'],
        ], [
            'cidade.required' => 'Por favor, informe a cidade.',
            'nicho.required' => 'Por favor, informe o nicho.',
            'max_results.max' => "O número máximo de resultados permitido é {$maxApiFetches}.",
        ]);

        // Se não informado, usa o máximo permitido
        $maxResults = $validated['max_results'] ?? $maxApiFetches;

        // Verifica cota ANTES de criar prospecção
        $quotaCheck = $this->checkQuotaExceeded($userId);
        if ($quotaCheck['exceeded']) {
            return redirect()
                ->route('prospects.create')
                ->withErrors([
                    'quota' => $quotaCheck['message']
                ])
                ->withInput();
        }

        // Calcula cota disponível
        $usage = $this->getUsageData($userId);
        $availableDaily = max(0, $usage['daily']['quota'] - $usage['daily']['used']);
        $availableMonthly = max(0, $usage['monthly']['quota'] - $usage['monthly']['used']);
        $availableQuota = min($availableDaily, $availableMonthly);

        // Normaliza cidade para verificar duplicatas
        $cityNormalizer = app(\App\Services\CityNormalizationService::class);
        $normalizedCity = $cityNormalizer->normalizeCity($validated['cidade']);

        // Verifica se já existe pesquisa similar (reutilização)
        $existingSearch = \App\Models\UserSearch::where('user_id', $userId)
            ->where(function ($query) use ($validated, $normalizedCity) {
                $query->where('cidade', $validated['cidade'])
                      ->orWhere('normalized_cidade', $normalizedCity);
            })
            ->where('nicho', $validated['nicho'])
            ->where('status', 'completed')
            ->whereNotNull('raw_data')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($existingSearch && !empty($existingSearch->raw_data)) {
            // Reutiliza pesquisa existente - processa novamente os dados SEM chamar API
            // NÃO precisa verificar cota porque não cria novos prospects
            Log::info('Reusing existing search in controller', [
                'user_id' => $userId,
                'cidade' => $validated['cidade'],
                'nicho' => $validated['nicho'],
                'existing_search_id' => $existingSearch->id,
            ]);

            \App\Jobs\ProcessProspectingJob::dispatch(
                $userId,
                $validated['cidade'],
                $validated['nicho'],
                $maxResults,
                $validated['servico'] ?? null,
                $request->boolean('only_valid_email', false),
                $request->boolean('only_valid_site', false)
            );
            
            return redirect()
                ->route('prospects.index')
                ->with('info', 'Pesquisa similar encontrada! Reutilizando dados (SEM custo de API)...');
        }

        // Se o número de resultados exceder a cota disponível, quebra em múltiplos jobs
        if ($maxResults > $availableQuota && $availableQuota > 0) {
            $jobsCount = ceil($maxResults / $availableQuota);
            $resultsPerJob = ceil($maxResults / $jobsCount);
            
            Log::info('Splitting prospecting into multiple jobs', [
                'user_id' => $userId,
                'max_results' => $maxResults,
                'available_quota' => $availableQuota,
                'jobs_count' => $jobsCount,
                'results_per_job' => $resultsPerJob,
            ]);

            // Cria múltiplos jobs
            for ($i = 0; $i < $jobsCount; $i++) {
                $jobResults = min($resultsPerJob, $maxResults - ($i * $resultsPerJob));
                
                if ($jobResults > 0) {
                    ProcessProspectingJob::dispatch(
                        $userId,
                        $validated['cidade'],
                        $validated['nicho'],
                        $jobResults,
                        $validated['servico'] ?? null,
                        $request->boolean('only_valid_email', false),
                        $request->boolean('only_valid_site', false)
                    );
                }
            }

            return redirect()
                ->route('prospects.index')
                ->with('success', "Prospecção iniciada! A busca foi dividida em {$jobsCount} job(s) para respeitar sua cota disponível. Os resultados aparecerão em alguns instantes.");
        }

        // Dispatch do job único para processar em background
        ProcessProspectingJob::dispatch(
            $userId,
            $validated['cidade'],
            $validated['nicho'],
            $maxResults,
            $validated['servico'] ?? null,
            $request->boolean('only_valid_email', false),
            $request->boolean('only_valid_site', false)
        );

        return redirect()
            ->route('prospects.index')
            ->with('success', 'Prospecção iniciada! Os resultados aparecerão em alguns instantes.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Prospect $prospect)
    {
        // Garante que o usuário só veja seus próprios prospects
        if ($prospect->user_id !== Auth::id()) {
            abort(403);
        }

        $prospect->load('lead');

        // Mensagem padrão para WhatsApp
        $whatsappMessage = ContactMessageTemplate::where('channel', 'whatsapp')
            ->where('is_active', true)
            ->orderBy('id')
            ->value('content');

        return view('prospects.show', [
            'prospect' => $prospect,
            'whatsappMessage' => $whatsappMessage,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prospect $prospect)
    {
        // Garante que o usuário só delete seus próprios prospects
        if ($prospect->user_id !== Auth::id()) {
            abort(403);
        }

        $prospect->delete();

        return redirect()
            ->route('prospects.index')
            ->with('success', 'Prospect removido com sucesso.');
    }

    /**
     * Export prospects to CSV
     */
    public function export(Request $request): StreamedResponse
    {
        $query = Prospect::forUser(Auth::id())
            ->orderBy('created_at', 'desc');

        // Aplica filtros se existirem
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        $prospects = $query->get();

        $filename = 'prospects_' . date('Y-m-d_His') . '.csv';

        return new StreamedResponse(function () use ($prospects) {
            $handle = fopen('php://output', 'w');

            // BOM para UTF-8 (Excel)
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Cabeçalhos
            fputcsv($handle, [
                'Nome',
                'Telefone',
                'WhatsApp',
                'E-mail',
                'Site',
                'Endereço',
                'Cidade',
                'Nicho',
                'Google Maps URL',
                'Status',
                'Data de Criação',
            ], ';');

            // Dados
            foreach ($prospects as $prospect) {
                fputcsv($handle, [
                    $prospect->nome,
                    $prospect->telefone ?? '',
                    $prospect->whatsapp ?? '',
                    $prospect->email ?? '',
                    $prospect->site ?? '',
                    $prospect->endereco ?? '',
                    $prospect->cidade,
                    $prospect->nicho,
                    $prospect->google_maps_url ?? '',
                    $prospect->status,
                    $prospect->created_at->format('d/m/Y H:i:s'),
                ], ';');
            }

            fclose($handle);
        }, Response::HTTP_OK, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Verifica novos prospects (para atualização em tempo real)
     */
    public function checkNew(Request $request)
    {
        $lastId = $request->get('last_id', 0);
        $count = $request->get('count', 0);

        $newProspects = Prospect::forUser(Auth::id())
            ->where('id', '>', $lastId)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get(['id', 'nome', 'cidade', 'nicho', 'status', 'created_at']);

        $totalCount = Prospect::forUser(Auth::id())->count();

        return response()->json([
            'new_prospects' => $newProspects,
            'new_count' => $newProspects->count(),
            'total_count' => $totalCount,
            'has_new' => $newProspects->count() > 0 || $totalCount != $count,
        ]);
    }

    /**
     * Marca ou atualiza um lead relacionado ao prospect.
     */
    public function storeLead(Request $request, Prospect $prospect)
    {
        if ($prospect->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'opportunity_value' => ['nullable', 'numeric', 'min:0'],
            'probability' => ['nullable', 'integer', 'min:0', 'max:100'],
            'stage' => ['nullable', 'string', 'max:100'],
            'expected_close_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'is_private' => ['nullable', 'boolean'],
        ]);

        $validated['user_id'] = Auth::id();
        $validated['prospect_id'] = $prospect->id;
        $validated['is_private'] = $request->boolean('is_private', true);

        ProspectLead::updateOrCreate(
            ['prospect_id' => $prospect->id],
            $validated
        );

        return redirect()
            ->route('prospects.show', $prospect)
            ->with('success', 'Lead atualizado com sucesso!');
    }

    /**
     * Verifica se a cota foi excedida
     */
    private function checkQuotaExceeded(int $userId): array
    {
        $usage = $this->getUsageData($userId);
        
        $dailyExceeded = $usage['daily']['quota'] > 0 && $usage['daily']['used'] >= $usage['daily']['quota'];
        $monthlyExceeded = $usage['monthly']['quota'] > 0 && $usage['monthly']['used'] >= $usage['monthly']['quota'];

        // Calcula tempo até reinício
        $dailyReset = null;
        $monthlyReset = null;

        if ($dailyExceeded) {
            // Próxima meia-noite
            $tomorrow = now()->copy()->addDay()->startOfDay();
            $now = now();
            $secondsUntilReset = $tomorrow->diffInSeconds($now);
            $hours = floor($secondsUntilReset / 3600);
            $minutes = floor(($secondsUntilReset % 3600) / 60);
            
            $dailyReset = [
                'hours' => $hours,
                'minutes' => $minutes,
                'timestamp' => $tomorrow->toIso8601String(),
            ];
        }

        if ($monthlyExceeded) {
            // Primeiro dia do próximo mês
            $nextMonth = now()->copy()->addMonth()->startOfMonth();
            $now = now();
            $secondsUntilReset = $nextMonth->diffInSeconds($now);
            $days = floor($secondsUntilReset / 86400);
            $hours = floor(($secondsUntilReset % 86400) / 3600);
            $minutes = floor(($secondsUntilReset % 3600) / 60);
            
            $monthlyReset = [
                'days' => $days,
                'hours' => $hours,
                'minutes' => $minutes,
                'timestamp' => $nextMonth->toIso8601String(),
            ];
        }

        if ($dailyExceeded && $monthlyExceeded) {
            return [
                'exceeded' => true,
                'message' => "Você atingiu o limite diário ({$usage['daily']['used']}/{$usage['daily']['quota']}) e mensal ({$usage['monthly']['used']}/{$usage['monthly']['quota']}) de prospects. Entre em contato para aumentar sua cota ou aguarde até o próximo período.",
                'type' => 'both',
                'daily' => ['exceeded' => true, 'reset_at' => $dailyReset],
                'monthly' => ['exceeded' => true, 'reset_at' => $monthlyReset],
            ];
        }

        if ($dailyExceeded) {
            return [
                'exceeded' => true,
                'message' => "Você atingiu o limite diário de prospects ({$usage['daily']['used']}/{$usage['daily']['quota']}). Tente novamente amanhã ou entre em contato para aumentar sua cota.",
                'type' => 'daily',
                'daily' => ['exceeded' => true, 'reset_at' => $dailyReset],
                'monthly' => ['exceeded' => false, 'reset_at' => null],
            ];
        }

        if ($monthlyExceeded) {
            return [
                'exceeded' => true,
                'message' => "Você atingiu o limite mensal de prospects ({$usage['monthly']['used']}/{$usage['monthly']['quota']}). Entre em contato para aumentar sua cota ou aguarde o próximo mês.",
                'type' => 'monthly',
                'daily' => ['exceeded' => false, 'reset_at' => null],
                'monthly' => ['exceeded' => true, 'reset_at' => $monthlyReset],
            ];
        }

        return [
            'exceeded' => false,
            'message' => '',
            'type' => null,
            'daily' => ['exceeded' => false, 'reset_at' => null],
            'monthly' => ['exceeded' => false, 'reset_at' => null],
        ];
    }

    /**
     * Calcula dados de uso de cota (diária e mensal) para o usuário.
     */
    private function getUsageData(int $userId): array
    {
        $user = \App\Models\User::with('plan')->find($userId);
        
        if (!$user) {
            // Fallback para valores padrão se usuário não existir
            return $this->getDefaultUsageData();
        }

        // Recarrega o usuário para garantir valores atualizados (incluindo quotas customizadas)
        $user->refresh();

        // Usa os métodos do User que já verificam quotas customizadas
        $monthlyQuota = $user->getEffectiveMonthlyQuota();
        $dailyQuota = $user->getEffectiveDailyQuota();

        $today = now()->startOfDay();
        $monthStart = now()->startOfMonth();

        $dailyCount = Prospect::forUser($userId)
            ->where('created_at', '>=', $today)
            ->count();

        $monthlyCount = Prospect::forUser($userId)
            ->where('created_at', '>=', $monthStart)
            ->count();

        $dailyPercent = $dailyQuota > 0 ? min(100, round(($dailyCount / $dailyQuota) * 100)) : null;
        $monthlyPercent = $monthlyQuota > 0 ? min(100, round(($monthlyCount / $monthlyQuota) * 100)) : null;

        return [
            'daily' => [
                'quota' => $dailyQuota,
                'used' => $dailyCount,
                'percent' => $dailyPercent,
            ],
            'monthly' => [
                'quota' => $monthlyQuota,
                'used' => $monthlyCount,
                'percent' => $monthlyPercent,
            ],
        ];
    }

    /**
     * Retorna dados padrão de uso (quando usuário não existe)
     */
    private function getDefaultUsageData(): array
    {
        $monthlyQuota = AppSetting::get('default_monthly_prospect_quota', 500);
        $dailyQuota = AppSetting::get('default_daily_prospect_quota', 60);

        return [
            'daily' => [
                'quota' => $dailyQuota,
                'used' => 0,
                'percent' => 0,
            ],
            'monthly' => [
                'quota' => $monthlyQuota,
                'used' => 0,
                'percent' => 0,
            ],
        ];
    }

    /**
     * Lista todas as pesquisas salvas do usuário agrupadas por cidade e nicho
     */
    public function mySearches(Request $request)
    {
        $userId = Auth::id();

        // Busca pesquisas completas com raw_data (pesquisas salvas)
        $searches = UserSearch::where('user_id', $userId)
            ->where('status', 'completed')
            ->whereNotNull('raw_data')
            ->orderBy('created_at', 'desc')
            ->get();

        // Agrupa por cidade + nicho (usando normalized_cidade quando disponível)
        $groupedSearches = [];
        foreach ($searches as $search) {
            $key = ($search->normalized_cidade ?? $search->cidade) . '|' . $search->nicho;
            
            if (!isset($groupedSearches[$key])) {
                $groupedSearches[$key] = [
                    'cidade' => $search->cidade,
                    'normalized_cidade' => $search->normalized_cidade ?? $search->cidade,
                    'nicho' => $search->nicho,
                    'search_id' => $search->id,
                    'results_count' => count($search->raw_data ?? []),
                    'created_at' => $search->created_at,
                    'updated_at' => $search->updated_at,
                ];
            } else {
                // Mantém a pesquisa mais recente
                if ($search->created_at > $groupedSearches[$key]['created_at']) {
                    $groupedSearches[$key]['search_id'] = $search->id;
                    $groupedSearches[$key]['results_count'] = count($search->raw_data ?? []);
                    $groupedSearches[$key]['created_at'] = $search->created_at;
                    $groupedSearches[$key]['updated_at'] = $search->updated_at;
                }
            }
        }

        // Conta prospects para cada pesquisa
        foreach ($groupedSearches as $key => &$group) {
            $prospectCount = Prospect::forUser($userId)
                ->where(function ($query) use ($group) {
                    $query->where('cidade', $group['cidade'])
                          ->orWhere('cidade', $group['normalized_cidade']);
                })
                ->where('nicho', $group['nicho'])
                ->count();
            
            $group['prospect_count'] = $prospectCount;
        }

        // Ordena por data de criação (mais recente primeiro)
        usort($groupedSearches, function ($a, $b) {
            return $b['created_at'] <=> $a['created_at'];
        });

        // Verifica se a cota foi excedida
        $quotaCheck = $this->checkQuotaExceeded($userId);
        $user = Auth::user();

        return view('searches.my', [
            'searches' => $groupedSearches,
            'quotaData' => $quotaCheck,
            'user' => $user,
        ]);
    }

    /**
     * Busca mais resultados para uma pesquisa específica
     */
    public function searchMore(Request $request, int $searchId)
    {
        $search = UserSearch::findOrFail($searchId);
        
        // Verifica se a pesquisa pertence ao usuário
        if ($search->user_id !== Auth::id()) {
            abort(403);
        }

        // Valida o número de resultados solicitado
        $user = Auth::user();
        $user->refresh();
        $maxApiFetches = $user->getEffectiveMaxApiFetches();

        $validated = $request->validate([
            'max_results' => ['required', 'integer', 'min:1', "max:{$maxApiFetches}"],
        ], [
            'max_results.required' => 'Por favor, informe quantos resultados buscar.',
            'max_results.max' => "O número máximo de resultados permitido é {$maxApiFetches}.",
        ]);

        $maxResults = $validated['max_results'];

        // Verifica cota ANTES de buscar mais resultados
        $quotaCheck = $this->checkQuotaExceeded(Auth::id());
        if ($quotaCheck['exceeded']) {
            return redirect()
                ->route('searches.my')
                ->withErrors(['quota' => $quotaCheck['message']]);
        }

        // Dispatch job para buscar mais resultados
        ProcessProspectingJob::dispatch(
            Auth::id(),
            $search->cidade,
            $search->nicho,
            $maxResults,
            $search->servico,
            $search->only_valid_email ?? false,
            $search->only_valid_site ?? false
        );

        return redirect()
            ->route('searches.my')
            ->with('success', "Buscando {$maxResults} resultado(s) para {$search->cidade} - {$search->nicho}... Os novos prospects aparecerão em alguns instantes.");
    }

    /**
     * Exporta prospects de uma pesquisa específica para CSV
     */
    public function exportSearchCsv(int $searchId)
    {
        $search = UserSearch::findOrFail($searchId);
        
        // Verifica se a pesquisa pertence ao usuário
        if ($search->user_id !== Auth::id()) {
            abort(403);
        }

        $normalizedCity = $search->normalized_cidade ?? $search->cidade;

        $prospects = Prospect::forUser(Auth::id())
            ->where(function ($query) use ($search, $normalizedCity) {
                $query->where('cidade', $search->cidade)
                      ->orWhere('cidade', $normalizedCity);
            })
            ->where('nicho', $search->nicho)
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'prospects_' . str_replace(' ', '_', $search->cidade) . '_' . str_replace(' ', '_', $search->nicho) . '_' . date('Y-m-d_His') . '.csv';

        return new StreamedResponse(function () use ($prospects) {
            $handle = fopen('php://output', 'w');

            // BOM para UTF-8 (Excel)
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Cabeçalhos
            fputcsv($handle, [
                'Nome',
                'Telefone',
                'WhatsApp',
                'E-mail',
                'Site',
                'Endereço',
                'Cidade',
                'Nicho',
                'Google Maps URL',
                'Status',
                'Data de Criação',
            ], ';');

            // Dados
            foreach ($prospects as $prospect) {
                fputcsv($handle, [
                    $prospect->nome,
                    $prospect->telefone ?? '',
                    $prospect->whatsapp ?? '',
                    $prospect->email ?? '',
                    $prospect->site ?? '',
                    $prospect->endereco ?? '',
                    $prospect->cidade,
                    $prospect->nicho,
                    $prospect->google_maps_url ?? '',
                    $prospect->status,
                    $prospect->created_at->format('d/m/Y H:i:s'),
                ], ';');
            }

            fclose($handle);
        }, Response::HTTP_OK, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Exporta prospects de uma pesquisa específica para XLSX
     */
    public function exportSearchXlsx(int $searchId)
    {
        $search = UserSearch::findOrFail($searchId);
        
        // Verifica se a pesquisa pertence ao usuário
        if ($search->user_id !== Auth::id()) {
            abort(403);
        }

        $normalizedCity = $search->normalized_cidade ?? $search->cidade;

        $prospects = Prospect::forUser(Auth::id())
            ->where(function ($query) use ($search, $normalizedCity) {
                $query->where('cidade', $search->cidade)
                      ->orWhere('cidade', $normalizedCity);
            })
            ->where('nicho', $search->nicho)
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'prospects_' . str_replace(' ', '_', $search->cidade) . '_' . str_replace(' ', '_', $search->nicho) . '_' . date('Y-m-d_His') . '.xlsx';

        // Verifica se PhpSpreadsheet está disponível
        if (class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
            return $this->exportXlsxWithPhpSpreadsheet($prospects, $filename);
        }

        // Fallback: retorna CSV (Excel pode abrir CSV)
        return $this->exportSearchCsv($search);
    }

    /**
     * Ativa 30 buscas gratuitas para o usuário (limitado a 1 vez)
     */
    public function activateFreeSearches(Request $request)
    {
        $user = Auth::user();
        $user->refresh();

        // Verifica se já usou as buscas gratuitas
        if ($user->free_searches_used) {
            return redirect()
                ->back()
                ->withErrors(['quota' => 'Você já utilizou suas buscas gratuitas. Entre em contato para mais créditos.']);
        }

        // Adiciona 30 buscas à cota diária e mensal
        $currentDailyQuota = $user->getEffectiveDailyQuota();
        $currentMonthlyQuota = $user->getEffectiveMonthlyQuota();
        
        // Se não tem quota customizada, usa a do plano e adiciona 30
        if (!$user->daily_quota_custom) {
            $user->daily_quota_custom = $currentDailyQuota + 30;
        } else {
            $user->daily_quota_custom += 30;
        }
        
        if (!$user->monthly_quota_custom) {
            $user->monthly_quota_custom = $currentMonthlyQuota + 30;
        } else {
            $user->monthly_quota_custom += 30;
        }
        
        $user->free_searches_used = true;
        $user->save();

        return redirect()
            ->back()
            ->with('success', 'Parabéns! Você ganhou 30 buscas gratuitas! Agora você pode continuar prospectando.');
    }

    /**
     * Exporta para XLSX usando PhpSpreadsheet
     */
    private function exportXlsxWithPhpSpreadsheet($prospects, $filename)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Cabeçalhos
        $headers = [
            'A1' => 'Nome',
            'B1' => 'Telefone',
            'C1' => 'WhatsApp',
            'D1' => 'E-mail',
            'E1' => 'Site',
            'F1' => 'Endereço',
            'G1' => 'Cidade',
            'H1' => 'Nicho',
            'I1' => 'Google Maps URL',
            'J1' => 'Status',
            'K1' => 'Data de Criação',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Estiliza cabeçalhos
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
        ];
        $sheet->getStyle('A1:K1')->applyFromArray($headerStyle);

        // Dados
        $row = 2;
        foreach ($prospects as $prospect) {
            $sheet->setCellValue('A' . $row, $prospect->nome);
            $sheet->setCellValue('B' . $row, $prospect->telefone ?? '');
            $sheet->setCellValue('C' . $row, $prospect->whatsapp ?? '');
            $sheet->setCellValue('D' . $row, $prospect->email ?? '');
            $sheet->setCellValue('E' . $row, $prospect->site ?? '');
            $sheet->setCellValue('F' . $row, $prospect->endereco ?? '');
            $sheet->setCellValue('G' . $row, $prospect->cidade);
            $sheet->setCellValue('H' . $row, $prospect->nicho);
            $sheet->setCellValue('I' . $row, $prospect->google_maps_url ?? '');
            $sheet->setCellValue('J' . $row, $prospect->status);
            $sheet->setCellValue('K' . $row, $prospect->created_at->format('d/m/Y H:i:s'));
            $row++;
        }

        // Ajusta largura das colunas
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Cria writer e retorna resposta
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}

