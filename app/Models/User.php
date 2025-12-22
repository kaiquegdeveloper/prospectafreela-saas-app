<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'refunded_at',
        'results_limit',
        'monthly_quota_custom',
        'daily_quota_custom',
        'max_api_fetches_custom',
        'plan_id',
        'free_searches_used',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'refunded_at' => 'datetime',
        ];
    }

    /**
     * Get the plan associated with the user.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the prospects for the user.
     */
    public function prospects()
    {
        return $this->hasMany(Prospect::class);
    }

    /**
     * Get the API logs for the user.
     */
    public function apiLogs()
    {
        return $this->hasMany(ApiLog::class);
    }

    /**
     * Get the payments for the user.
     */
    public function payments()
    {
        return $this->hasMany(UserPayment::class);
    }

    /**
     * Get the searches for the user.
     */
    public function searches()
    {
        return $this->hasMany(UserSearch::class);
    }

    /**
     * Get the login history for the user.
     */
    public function loginHistory(): HasMany
    {
        return $this->hasMany(UserLoginHistory::class);
    }

    /**
     * Get the modules for the user.
     */
    public function modules(): HasMany
    {
        return $this->hasMany(UserModule::class);
    }

    /**
     * Check if user is super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Get effective monthly quota (custom or from plan)
     */
    public function getEffectiveMonthlyQuota(): int
    {
        // Prioridade: quota customizada > quota do plano > padrão
        if ($this->monthly_quota_custom !== null) {
            return $this->monthly_quota_custom;
        }

        // Garante que o relacionamento plan está carregado
        if (!$this->relationLoaded('plan') && $this->plan_id) {
            $this->load('plan');
        }

        return $this->plan?->monthly_prospect_quota ?? 500;
    }

    /**
     * Get effective daily quota (custom or from plan)
     */
    public function getEffectiveDailyQuota(): int
    {
        // Prioridade: quota customizada > quota do plano > padrão
        if ($this->daily_quota_custom !== null) {
            return $this->daily_quota_custom;
        }

        // Garante que o relacionamento plan está carregado
        if (!$this->relationLoaded('plan') && $this->plan_id) {
            $this->load('plan');
        }

        return $this->plan?->daily_prospect_quota ?? 60;
    }

    /**
     * Get effective max API fetches (custom or default)
     */
    public function getEffectiveMaxApiFetches(): int
    {
        // Se tem quota customizada, retorna ela
        if ($this->max_api_fetches_custom !== null) {
            return $this->max_api_fetches_custom;
        }

        // Padrão: 20 resultados
        return 20;
    }

    /**
     * Check if user has access to a module
     */
    public function hasModuleAccess(string $moduleName): bool
    {
        // Super admin always has access
        if ($this->isSuperAdmin()) {
            return true;
        }

        $module = $this->modules()->where('module_name', $moduleName)->first();
        
        // If no record exists, default to enabled
        if (!$module) {
            return true;
        }

        return $module->is_enabled;
    }

    /**
     * Get last login date
     */
    public function getLastLoginAt(): ?\Carbon\Carbon
    {
        $lastLogin = $this->loginHistory()
            ->orderBy('logged_in_at', 'desc')
            ->first();

        return $lastLogin?->logged_in_at;
    }

    /**
     * Get total login count
     */
    public function getLoginCount(): int
    {
        return $this->loginHistory()->count();
    }
}
