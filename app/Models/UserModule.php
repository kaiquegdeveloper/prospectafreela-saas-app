<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserModule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'module_name',
        'is_enabled',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
        ];
    }

    /**
     * Get the user that owns the module.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Available module names
     */
    public static function availableModules(): array
    {
        return [
            'dashboard' => 'Dashboard',
            'prospects' => 'Prospects',
            'searches' => 'Pesquisas',
            'export' => 'Exportação',
            'plan' => 'Plano',
        ];
    }
}

