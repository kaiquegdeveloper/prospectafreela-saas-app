<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesScript extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'stage',
        'title',
        'content',
        'tips',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }

    /**
     * Get the category that owns this script.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(SalesScriptCategory::class, 'category_id');
    }

    /**
     * Scope to get only active scripts.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by stage.
     */
    public function scopeByStage($query, string $stage)
    {
        return $query->where('stage', $stage);
    }

    /**
     * Scope to filter by category.
     */
    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope to order scripts.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('created_at');
    }

    /**
     * Get stage name in Portuguese.
     */
    public function getStageNameAttribute(): string
    {
        return match($this->stage) {
            'introducao' => 'Introdução',
            'qualificacao' => 'Qualificação',
            'levar_call' => 'Levar para a Call',
            'quebra_objecao' => 'Quebra de Objeção',
            'fechamento' => 'Fechamento',
            default => $this->stage,
        };
    }
}

