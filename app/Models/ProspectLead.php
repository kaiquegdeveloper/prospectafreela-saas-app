<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProspectLead extends Model
{
    use HasFactory;

    protected $fillable = [
        'prospect_id',
        'user_id',
        'opportunity_value',
        'probability',
        'stage',
        'expected_close_date',
        'notes',
        'is_private',
    ];

    protected function casts(): array
    {
        return [
            'opportunity_value' => 'decimal:2',
            'expected_close_date' => 'date',
            'is_private' => 'boolean',
        ];
    }

    public function prospect(): BelongsTo
    {
        return $this->belongsTo(Prospect::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}


