<?php

namespace App\Http\Controllers;

use App\Models\ApiLog;
use App\Models\AppSetting;
use App\Models\Plan;
use App\Models\QueuePause;
use App\Models\User;
use App\Models\UserLoginHistory;
use App\Models\UserModule;
use App\Models\UserPayment;
use App\Models\UserSearch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;

class SuperAdminController extends Controller
{
    /**
     * Dashboard principal
     */
    public function dashboard()
    {
        // Users Ativos Logados (últimas 24h)
        $activeUsers = DB::table('sessions')
            ->where('last_activity', '>=', now()->subDay()->timestamp)
            ->distinct('user_id')
            ->count('user_id');

        // Users Totais
        $totalUsers = User::count();

        // Faturamento total (considerando apenas pagamentos não reembolsados)
        $totalRevenue = UserPayment::where('refunded', false)->sum('amount');

        // Faturamento mensal (MRR) baseado nos planos ativos e não reembolsados
        $payingUsers = User::where('role', '!=', 'super_admin')
            ->whereNull('refunded_at')
            ->where('is_active', true)
            ->whereNotNull('plan_id')
            ->with('plan')
            ->get();
        $monthlyRevenue = $payingUsers->sum(fn ($user) => $user->plan?->price ?? 0);

        // Taxa de reembolso global
        $totalPayments = UserPayment::count();
        $refundedPayments = UserPayment::where('refunded', true)->count();
        $refundRate = $totalPayments > 0 ? round(($refundedPayments / $totalPayments) * 100, 1) : 0;

        // Usos da API (últimos 30 dias)
        $apiUsage30Days = ApiLog::where('created_at', '>=', now()->subDays(30))
            ->where('api_name', 'google_maps_places')
            ->count();

        // Custo estimado da API (últimos 30 dias)
        $apiCost30Days = ApiLog::where('created_at', '>=', now()->subDays(30))
            ->where('api_name', 'google_maps_places')
            ->sum('cost');

        // Detecção de anomalias
        $anomalies = $this->detectAnomalies();

        // Estatísticas de uso por usuário (últimos 7 dias)
        $userUsageStats = UserSearch::select('user_id', DB::raw('count(*) as searches_count'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('user_id')
            ->orderBy('searches_count', 'desc')
            ->with('user')
            ->limit(10)
            ->get();

        return view('super-admin.dashboard', compact(
            'activeUsers',
            'totalUsers',
            'totalRevenue',
            'monthlyRevenue',
            'apiUsage30Days',
            'apiCost30Days',
            'anomalies',
            'userUsageStats',
            'refundRate',
        ));
    }

    /**
     * Lista de usuários
     */
    public function users(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->withCount(['prospects', 'searches', 'payments'])
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('super-admin.users', compact('users'));
    }

    /**
     * Toggle status do usuário (enable/disable)
     */
    public function toggleUserStatus(User $user)
    {
        $newStatus = !$user->is_active;
        $user->update(['is_active' => $newStatus]);
        $user->refresh(); // Recarrega o modelo para garantir que o valor está atualizado

        return redirect()->back()->with('success', 
            "Usuário {$user->name} foi " . ($newStatus ? 'habilitado' : 'desabilitado') . " com sucesso."
        );
    }

    /**
     * Marcar/Desmarcar reembolso de um usuário
     * Se reembolsado, desativa o acesso.
     */
    public function toggleRefund(User $user)
    {
        $isRefunded = $user->refunded_at !== null;

        if ($isRefunded) {
            // Reverter reembolso
            $user->update([
                'refunded_at' => null,
                'is_active' => true,
            ]);

            // Desmarca o pagamento mais recente
            $lastPayment = $user->payments()->latest('payment_date')->first();
            if ($lastPayment) {
                $lastPayment->update([
                    'refunded' => false,
                    'refunded_at' => null,
                ]);
            }

            return redirect()->back()->with('success', "Reembolso removido e acesso reativado para {$user->name}.");
        }

        // Marca reembolso e desativa acesso
        $user->update([
            'refunded_at' => now(),
            'is_active' => false,
        ]);

        // Marca o pagamento mais recente como reembolsado
        $lastPayment = $user->payments()->latest('payment_date')->first();
        if ($lastPayment) {
            $lastPayment->update([
                'refunded' => true,
                'refunded_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', "Usuário {$user->name} marcado como reembolsado e acesso desativado.");
    }

    /**
     * Gestão de locais (pesquisas)
     */
    public function searches(Request $request)
    {
        $query = UserSearch::with('user');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('cidade')) {
            $query->where('cidade', 'like', "%{$request->cidade}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        $searches = $query->orderBy('created_at', 'desc')
            ->paginate(30)
            ->withQueryString();

        $users = User::orderBy('name')->get(['id', 'name', 'email']);

        return view('super-admin.searches', compact('searches', 'users'));
    }

    /**
     * Gestão de pagamentos
     */
    public function payments(Request $request)
    {
        $query = UserPayment::with('user');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->where('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('payment_date', '<=', $request->date_to);
        }

        $payments = $query->orderBy('payment_date', 'desc')
            ->paginate(30)
            ->withQueryString();

        $users = User::orderBy('name')->get(['id', 'name', 'email']);

        return view('super-admin.payments', compact('payments', 'users'));
    }

    /**
     * Criar novo pagamento
     */
    public function storePayment(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:one_time,monthly',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        UserPayment::create($validated);

        return redirect()->back()->with('success', 'Pagamento registrado com sucesso!');
    }

    /**
     * Configurações globais
     */
    public function settings()
    {
        $settings = AppSetting::all()->keyBy('key');
        
        return view('super-admin.settings', compact('settings'));
    }

    /**
     * Atualizar configurações
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'api_rate_limit_per_minute' => 'required|integer|min:1|max:1000',
            'default_results_limit' => 'required|integer|min:1|max:500',
        ]);

        AppSetting::set('api_rate_limit_per_minute', $validated['api_rate_limit_per_minute'], 'integer', 
            'Limite de chamadas da API por minuto');
        
        AppSetting::set('default_results_limit', $validated['default_results_limit'], 'integer', 
            'Limite padrão de resultados por busca');

        return redirect()->back()->with('success', 'Configurações atualizadas com sucesso!');
    }

    /**
     * Atualizar limite de resultados de um usuário
     */
    public function updateUserResultsLimit(Request $request, User $user)
    {
        $validated = $request->validate([
            'results_limit' => 'nullable|integer|min:1|max:500',
        ]);

        $user->update([
            'results_limit' => $validated['results_limit'] ?? null,
        ]);

        return redirect()->back()->with('success', 
            "Limite de resultados do usuário {$user->name} atualizado com sucesso!"
        );
    }

    /**
     * Atualizar plano e quotas customizadas do usuário
     */
    public function updateUserPlan(Request $request, User $user)
    {
        $validated = $request->validate([
            'plan_id' => 'nullable|exists:plans,id',
            'monthly_quota_custom' => 'nullable|integer|min:0',
            'daily_quota_custom' => 'nullable|integer|min:0',
            'max_api_fetches_custom' => 'nullable|integer|min:1',
        ]);

        $user->update([
            'plan_id' => $validated['plan_id'] ?? null,
            'monthly_quota_custom' => $validated['monthly_quota_custom'] ?? null,
            'daily_quota_custom' => $validated['daily_quota_custom'] ?? null,
            'max_api_fetches_custom' => $validated['max_api_fetches_custom'] ?? null,
        ]);

        return redirect()->back()->with('success', 
            "Plano e quotas do usuário {$user->name} atualizados com sucesso!"
        );
    }

    /**
     * Ver histórico de login de um usuário
     */
    public function userLoginHistory(User $user)
    {
        $loginHistory = $user->loginHistory()
            ->orderBy('logged_in_at', 'desc')
            ->paginate(50);

        return view('super-admin.user-login-history', compact('user', 'loginHistory'));
    }

    /**
     * Reports - Usuários que não logaram
     */
    public function reportsUsersNotLoggedIn(Request $request)
    {
        $days = $request->get('days', 30);
        $cutoffDate = now()->subDays($days);

        $usersNotLoggedIn = User::whereDoesntHave('loginHistory', function ($query) use ($cutoffDate) {
            $query->where('logged_in_at', '>=', $cutoffDate);
        })
        ->where('role', '!=', 'super_admin')
        ->with('plan')
        ->orderBy('created_at', 'desc')
        ->paginate(50)
        ->withQueryString();

        return view('super-admin.reports.users-not-logged-in', compact('usersNotLoggedIn', 'days'));
    }

    /**
     * Reports - Usuários que logaram hoje
     */
    public function reportsUsersLoggedInToday()
    {
        $usersLoggedInToday = User::whereHas('loginHistory', function ($query) {
            $query->whereDate('logged_in_at', today());
        })
        ->with(['plan', 'loginHistory' => function ($query) {
            $query->whereDate('logged_in_at', today())
                  ->orderBy('logged_in_at', 'desc');
        }])
        ->orderBy('created_at', 'desc')
        ->paginate(50);

        return view('super-admin.reports.users-logged-in-today', compact('usersLoggedInToday'));
    }

    /**
     * Reports - Vezes que user logou
     */
    public function reportsUserLoginCounts(Request $request)
    {
        $query = User::withCount('loginHistory')
            ->where('role', '!=', 'super_admin')
            ->orderBy('login_history_count', 'desc');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $users = $query->paginate(50)->withQueryString();

        return view('super-admin.reports.user-login-counts', compact('users'));
    }

    /**
     * Gerenciar módulos de um usuário
     */
    public function userModules(User $user)
    {
        $availableModules = UserModule::availableModules();
        $userModules = $user->modules()->get()->keyBy('module_name');

        return view('super-admin.user-modules', compact('user', 'availableModules', 'userModules'));
    }

    /**
     * Impersonar um usuário (somente super admin)
     */
    public function impersonate(User $user)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        session(['impersonator_id' => auth()->id()]);
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', "Você está acessando como {$user->name}.");
    }

    /**
     * Encerrar impersonação e voltar para o super admin original
     */
    public function leaveImpersonation()
    {
        $impersonatorId = session('impersonator_id');

        if (!$impersonatorId) {
            return redirect()->route('dashboard');
        }

        Auth::loginUsingId($impersonatorId);
        session()->forget('impersonator_id');

        return redirect()->route('super-admin.dashboard')->with('success', 'Você voltou para sua conta de super admin.');
    }

    /**
     * Atualizar módulos de um usuário
     */
    public function updateUserModules(Request $request, User $user)
    {
        $availableModules = array_keys(UserModule::availableModules());
        $modulesData = $request->input('modules', []);

        foreach ($availableModules as $moduleName) {
            // Se o módulo está no array, está habilitado (checkbox marcado)
            // Se não está no array, está desabilitado (checkbox não marcado)
            $isEnabled = isset($modulesData[$moduleName]) && $modulesData[$moduleName] == '1';

            UserModule::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'module_name' => $moduleName,
                ],
                [
                    'is_enabled' => $isEnabled,
                ]
            );
        }

        return redirect()->back()->with('success', 
            "Módulos do usuário {$user->name} atualizados com sucesso!"
        );
    }

    /**
     * Monitorar filas
     */
    public function queues()
    {
        $queuePauses = QueuePause::with('pausedBy')
            ->orderBy('created_at', 'desc')
            ->get();

        $queueStats = [
            'prospecting' => [
                'pending' => DB::table('jobs')
                    ->where('queue', 'prospecting')
                    ->whereNull('reserved_at')
                    ->count(),
                'processing' => DB::table('jobs')
                    ->where('queue', 'prospecting')
                    ->whereNotNull('reserved_at')
                    ->count(),
                'failed' => DB::table('failed_jobs')
                    ->where('queue', 'prospecting')
                    ->count(),
            ],
        ];

        $isGlobalPaused = QueuePause::isQueuePaused(null);
        $isProspectingPaused = QueuePause::isQueuePaused('prospecting');

        return view('super-admin.queues', compact(
            'queuePauses',
            'queueStats',
            'isGlobalPaused',
            'isProspectingPaused'
        ));
    }

    /**
     * Pausar fila
     */
    public function pauseQueue(Request $request)
    {
        $validated = $request->validate([
            'queue_name' => 'nullable|string',
            'reason' => 'nullable|string|max:500',
        ]);

        $queueName = $validated['queue_name'] ?? null;
        $reason = $validated['reason'] ?? null;

        QueuePause::pause($queueName, $reason, auth()->id());

        $message = $queueName 
            ? "Fila '{$queueName}' pausada com sucesso!"
            : "Todas as filas foram pausadas com sucesso!";

        return redirect()->back()->with('success', $message);
    }

    /**
     * Retomar fila
     */
    public function resumeQueue(Request $request)
    {
        $validated = $request->validate([
            'queue_name' => 'nullable|string',
        ]);

        $queueName = $validated['queue_name'] ?? null;

        QueuePause::resume($queueName);

        $message = $queueName 
            ? "Fila '{$queueName}' retomada com sucesso!"
            : "Todas as filas foram retomadas com sucesso!";

        return redirect()->back()->with('success', $message);
    }

    /**
     * Controle de custos
     */
    public function costs(Request $request)
    {
        $period = $request->get('period', 30); // days
        $startDate = now()->subDays($period);
        $endDate = now();

        // API Costs
        $apiCosts = ApiLog::where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->select(
                'api_name',
                DB::raw('SUM(cost) as total_cost'),
                DB::raw('COUNT(*) as total_calls')
            )
            ->groupBy('api_name')
            ->get();

        $totalApiCost = $apiCosts->sum('total_cost');
        $totalApiCalls = $apiCosts->sum('total_calls');

        // Cost by user
        $costsByUser = ApiLog::where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->select(
                'user_id',
                DB::raw('SUM(cost) as total_cost'),
                DB::raw('COUNT(*) as total_calls')
            )
            ->groupBy('user_id')
            ->with('user')
            ->orderBy('total_cost', 'desc')
            ->limit(20)
            ->get();

        // Daily costs for chart
        $dailyCosts = ApiLog::where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(cost) as daily_cost'),
                DB::raw('COUNT(*) as daily_calls')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('super-admin.costs', compact(
            'apiCosts',
            'totalApiCost',
            'totalApiCalls',
            'costsByUser',
            'dailyCosts',
            'period',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Dashboard SaaS avançado
     */
    public function saasDashboard()
    {
        $currentPeriodStart = now()->subDays(30);
        $previousPeriodStart = now()->subDays(60);
        $previousPeriodEnd = now()->subDays(30);

        // User Growth
        $currentUsers = User::where('created_at', '>=', $currentPeriodStart)
            ->where('role', '!=', 'super_admin')
            ->count();
        $previousUsers = User::whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])
            ->where('role', '!=', 'super_admin')
            ->count();
        $userGrowth = $previousUsers > 0 
            ? (($currentUsers - $previousUsers) / $previousUsers) * 100 
            : ($currentUsers > 0 ? 100 : 0);

        // User Loss
        $currentLostUsers = User::where('is_active', false)
            ->where('updated_at', '>=', $currentPeriodStart)
            ->where('role', '!=', 'super_admin')
            ->count();
        $previousLostUsers = User::where('is_active', false)
            ->whereBetween('updated_at', [$previousPeriodStart, $previousPeriodEnd])
            ->where('role', '!=', 'super_admin')
            ->count();
        $userLoss = $previousLostUsers > 0 
            ? (($currentLostUsers - $previousLostUsers) / $previousLostUsers) * 100 
            : ($currentLostUsers > 0 ? 100 : 0);

        // MRR
        $currentMRR = UserPayment::where('type', 'monthly')
            ->where('payment_date', '>=', $currentPeriodStart)
            ->sum('amount');
        $previousMRR = UserPayment::where('type', 'monthly')
            ->whereBetween('payment_date', [$previousPeriodStart, $previousPeriodEnd])
            ->sum('amount');
        $mrrGrowth = $previousMRR > 0 
            ? (($currentMRR - $previousMRR) / $previousMRR) * 100 
            : ($currentMRR > 0 ? 100 : 0);

        // Churn Rate
        $totalActiveUsers = User::where('is_active', true)
            ->where('role', '!=', 'super_admin')
            ->count();
        $churnRate = $totalActiveUsers > 0 
            ? ($currentLostUsers / $totalActiveUsers) * 100 
            : 0;

        // LTV (Lifetime Value) - Average revenue per user
        $totalRevenue = UserPayment::sum('amount');
        $totalPayingUsers = UserPayment::distinct('user_id')->count('user_id');
        $ltv = $totalPayingUsers > 0 ? $totalRevenue / $totalPayingUsers : 0;

        // Daily metrics for chart
        $dailyMetrics = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $nextDate = $date->copy()->addDay();

            $dailyMetrics[] = [
                'date' => $date->format('Y-m-d'),
                'new_users' => User::whereBetween('created_at', [$date, $nextDate])
                    ->where('role', '!=', 'super_admin')
                    ->count(),
                'revenue' => UserPayment::whereBetween('payment_date', [$date, $nextDate])
                    ->sum('amount'),
                'active_users' => UserLoginHistory::whereDate('logged_in_at', $date->format('Y-m-d'))
                    ->distinct('user_id')
                    ->count('user_id'),
            ];
        }

        return view('super-admin.saas-dashboard', compact(
            'currentUsers',
            'previousUsers',
            'userGrowth',
            'currentLostUsers',
            'previousLostUsers',
            'userLoss',
            'currentMRR',
            'previousMRR',
            'mrrGrowth',
            'churnRate',
            'ltv',
            'dailyMetrics'
        ));
    }

    /**
     * Visualizar logs do Laravel
     */
    public function logs()
    {
        return view('super-admin.logs');
    }

    /**
     * Listar plans
     */
    public function plans()
    {
        $plans = Plan::orderBy('created_at', 'desc')->paginate(20);
        
        return view('super-admin.plans', compact('plans'));
    }

    /**
     * Criar novo plan
     */
    public function createPlan(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'monthly_prospect_quota' => 'required|integer|min:0',
            'daily_prospect_quota' => 'required|integer|min:0',
            'price' => 'nullable|numeric|min:0',
        ]);

        Plan::create([
            'name' => $validated['name'],
            'monthly_prospect_quota' => $validated['monthly_prospect_quota'],
            'daily_prospect_quota' => $validated['daily_prospect_quota'],
            'price' => $validated['price'] ?? 0,
            'is_active' => $request->input('is_active') == '1',
        ]);

        return redirect()->route('super-admin.plans')->with('success', 'Plano criado com sucesso!');
    }

    /**
     * Atualizar plan
     */
    public function updatePlan(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'monthly_prospect_quota' => 'required|integer|min:0',
            'daily_prospect_quota' => 'required|integer|min:0',
            'price' => 'nullable|numeric|min:0',
        ]);

        $plan->update([
            'name' => $validated['name'],
            'monthly_prospect_quota' => $validated['monthly_prospect_quota'],
            'daily_prospect_quota' => $validated['daily_prospect_quota'],
            'price' => $validated['price'] ?? 0,
            'is_active' => $request->input('is_active') == '1',
        ]);

        return redirect()->route('super-admin.plans')->with('success', 'Plano atualizado com sucesso!');
    }

    /**
     * Deletar plan
     */
    public function deletePlan(Plan $plan)
    {
        // Verifica se há usuários usando este plan
        $usersCount = $plan->users()->count();
        
        if ($usersCount > 0) {
            return redirect()->back()->with('error', 
                "Não é possível deletar o plano. Existem {$usersCount} usuário(s) usando este plano."
            );
        }

        $plan->delete();

        return redirect()->route('super-admin.plans')->with('success', 'Plano deletado com sucesso!');
    }

    /**
     * Detecção de anomalias
     */
    private function detectAnomalies(): array
    {
        $anomalies = [];

        // Anomalia 1: Pico de uso da API (mais de 3x a média nas últimas 24h)
        $avgApiCalls = ApiLog::where('created_at', '>=', now()->subDays(7))
            ->where('api_name', 'google_maps_places')
            ->count() / 7;

        $apiCallsLast24h = ApiLog::where('created_at', '>=', now()->subDay())
            ->where('api_name', 'google_maps_places')
            ->count();

        if ($apiCallsLast24h > ($avgApiCalls * 3) && $avgApiCalls > 0) {
            $anomalies[] = [
                'type' => 'api_spike',
                'severity' => 'high',
                'message' => "Pico de uso da API detectado: {$apiCallsLast24h} chamadas nas últimas 24h (média: " . round($avgApiCalls) . ")",
            ];
        }

        // Anomalia 2: Usuário com muitas pesquisas simultâneas
        $userWithManySearches = UserSearch::select('user_id', DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subHour())
            ->groupBy('user_id')
            ->having('count', '>', 10)
            ->with('user')
            ->first();

        if ($userWithManySearches) {
            $anomalies[] = [
                'type' => 'user_abuse',
                'severity' => 'medium',
                'message' => "Usuário {$userWithManySearches->user->name} realizou {$userWithManySearches->count} pesquisas na última hora",
            ];
        }

        // Anomalia 3: Taxa de erro alta
        $errorRate = ApiLog::where('created_at', '>=', now()->subDay())
            ->where('api_name', 'google_maps_places')
            ->where('status_code', '>=', 400)
            ->count();

        $totalCalls = ApiLog::where('created_at', '>=', now()->subDay())
            ->where('api_name', 'google_maps_places')
            ->count();

        if ($totalCalls > 0 && ($errorRate / $totalCalls) > 0.2) {
            $anomalies[] = [
                'type' => 'high_error_rate',
                'severity' => 'high',
                'message' => "Taxa de erro alta: " . round(($errorRate / $totalCalls) * 100, 1) . "% nas últimas 24h",
            ];
        }

        return $anomalies;
    }
}
