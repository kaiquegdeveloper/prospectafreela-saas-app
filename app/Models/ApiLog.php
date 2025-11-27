<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'api_name',
        'endpoint',
        'method',
        'status_code',
        'request_data',
        'response_data',
        'cost',
        'response_time_ms',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'cost' => 'decimal:4',
            'request_data' => 'array',
            'response_data' => 'array',
        ];
    }

    /**
     * Get the user that owns the API log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter by API name.
     */
    public function scopeForApi($query, string $apiName)
    {
        return $query->where('api_name', $apiName);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
