<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SalesScriptCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
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
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Get the scripts for this category.
     */
    public function scripts(): HasMany
    {
        return $this->hasMany(SalesScript::class, 'category_id');
    }

    /**
     * Get active scripts for this category.
     */
    public function activeScripts(): HasMany
    {
        return $this->hasMany(SalesScript::class, 'category_id')
            ->where('is_active', true)
            ->orderBy('order');
    }

    /**
     * Get scripts by stage.
     */
    public function scriptsByStage(string $stage): HasMany
    {
        return $this->hasMany(SalesScript::class, 'category_id')
            ->where('stage', $stage)
            ->where('is_active', true)
            ->orderBy('order');
    }

    /**
     * Scope to get only active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order categories.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }
}

