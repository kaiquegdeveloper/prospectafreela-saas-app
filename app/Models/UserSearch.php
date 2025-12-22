<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSearch extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cidade',
        'normalized_cidade',
        'nicho',
        'servico',
        'only_valid_email',
        'only_valid_site',
        'results_count',
        'status',
        'raw_data',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
            'raw_data' => 'array',
            'only_valid_email' => 'boolean',
            'only_valid_site' => 'boolean',
        ];
    }

    /**
     * Get the user that owns the search.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
