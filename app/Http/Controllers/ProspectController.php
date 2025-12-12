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

        return view('prospects.create', [
            'usage' => $usage,
            'maxApiFetches' => $maxApiFetches,
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
            'max_results' => ['nullable', 'integer', 'min:1', "max:{$maxApiFetches}"],
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
                $maxResults
            );
            
            return redirect()
                ->route('prospects.index')
                ->with('info', 'Pesquisa similar encontrada! Reutilizando dados (SEM custo de API)...');
        }

        // Dispatch do job para processar em background (só se não encontrou pesquisa anterior)
        ProcessProspectingJob::dispatch(
            $userId,
            $validated['cidade'],
            $validated['nicho'],
            $maxResults
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

        return view('prospects.show', compact('prospect'));
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

        if ($dailyExceeded && $monthlyExceeded) {
            return [
                'exceeded' => true,
                'message' => "Você atingiu o limite diário ({$usage['daily']['used']}/{$usage['daily']['quota']}) e mensal ({$usage['monthly']['used']}/{$usage['monthly']['quota']}) de prospects. Entre em contato para aumentar sua cota ou aguarde até o próximo período.",
                'type' => 'both'
            ];
        }

        if ($dailyExceeded) {
            return [
                'exceeded' => true,
                'message' => "Você atingiu o limite diário de prospects ({$usage['daily']['used']}/{$usage['daily']['quota']}). Tente novamente amanhã ou entre em contato para aumentar sua cota.",
                'type' => 'daily'
            ];
        }

        if ($monthlyExceeded) {
            return [
                'exceeded' => true,
                'message' => "Você atingiu o limite mensal de prospects ({$usage['monthly']['used']}/{$usage['monthly']['quota']}). Entre em contato para aumentar sua cota ou aguarde o próximo mês.",
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

        return view('searches.my', [
            'searches' => $groupedSearches,
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

        // Verifica cota ANTES de buscar mais resultados
        $quotaCheck = $this->checkQuotaExceeded(Auth::id());
        if ($quotaCheck['exceeded']) {
            return redirect()
                ->route('searches.my')
                ->withErrors(['quota' => $quotaCheck['message']]);
        }

        // Obtém o limite máximo de API fetches do usuário
        $user = Auth::user();
        $user->refresh();
        $maxApiFetches = $user->getEffectiveMaxApiFetches();

        // Dispatch job para buscar mais resultados (usa o máximo permitido)
        ProcessProspectingJob::dispatch(
            Auth::id(),
            $search->cidade,
            $search->nicho,
            $maxApiFetches
        );

        return redirect()
            ->route('searches.my')
            ->with('success', 'Buscando mais resultados para ' . $search->cidade . ' - ' . $search->nicho . '... Os novos prospects aparecerão em alguns instantes.');
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

