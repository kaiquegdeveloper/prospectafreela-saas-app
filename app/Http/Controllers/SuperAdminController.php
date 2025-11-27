<?php

namespace App\Http\Controllers;

use App\Models\ApiLog;
use App\Models\AppSetting;
use App\Models\User;
use App\Models\UserPayment;
use App\Models\UserSearch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

        // Faturamento Total
        $totalRevenue = UserPayment::sum('amount');

        // MRR (Monthly Recurring Revenue) - soma dos pagamentos mensais do mês atual
        $mrr = UserPayment::where('type', 'monthly')
            ->whereYear('payment_date', now()->year)
            ->whereMonth('payment_date', now()->month)
            ->sum('amount');

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
            'mrr',
            'apiUsage30Days',
            'apiCost30Days',
            'anomalies',
            'userUsageStats'
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
        $user->update(['is_active' => !$user->is_active]);

        return redirect()->back()->with('success', 
            "Usuário {$user->name} foi " . ($user->is_active ? 'habilitado' : 'desabilitado') . " com sucesso."
        );
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
