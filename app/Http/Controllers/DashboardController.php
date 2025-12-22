<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Prospect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $user->refresh();
        
        $usage = $this->getUsageData($user->id);
        $quotaCheck = $this->checkQuotaExceeded($user->id);

        return view('dashboard', [
            'usage' => $usage,
            'quotaData' => $quotaCheck,
            'user' => $user,
        ]);
    }

    /**
     * Calcula dados de uso de cota (diária e mensal) para o usuário.
     */
    private function getUsageData(int $userId): array
    {
        $user = \App\Models\User::with('plan')->find($userId);
        
        if (!$user) {
            $monthlyQuota = AppSetting::get('default_monthly_prospect_quota', 500);
            $dailyQuota = AppSetting::get('default_daily_prospect_quota', 60);
            return [
                'daily' => ['quota' => $dailyQuota, 'used' => 0, 'percent' => 0],
                'monthly' => ['quota' => $monthlyQuota, 'used' => 0, 'percent' => 0],
            ];
        }

        $user->refresh();
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
                'message' => "Você atingiu o limite diário ({$usage['daily']['used']}/{$usage['daily']['quota']}) e mensal ({$usage['monthly']['used']}/{$usage['monthly']['quota']}) de prospects.",
                'type' => 'both',
                'daily' => ['exceeded' => true, 'reset_at' => $dailyReset],
                'monthly' => ['exceeded' => true, 'reset_at' => $monthlyReset],
            ];
        }

        if ($dailyExceeded) {
            return [
                'exceeded' => true,
                'message' => "Você atingiu o limite diário de prospects ({$usage['daily']['used']}/{$usage['daily']['quota']}).",
                'type' => 'daily',
                'daily' => ['exceeded' => true, 'reset_at' => $dailyReset],
                'monthly' => ['exceeded' => false, 'reset_at' => null],
            ];
        }

        if ($monthlyExceeded) {
            return [
                'exceeded' => true,
                'message' => "Você atingiu o limite mensal de prospects ({$usage['monthly']['used']}/{$usage['monthly']['quota']}).",
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
}

