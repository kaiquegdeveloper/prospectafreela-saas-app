<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QueuePause extends Model
{
    use HasFactory;

    protected $fillable = [
        'queue_name',
        'is_paused',
        'reason',
        'paused_by',
        'paused_at',
        'resumed_at',
    ];

    protected function casts(): array
    {
        return [
            'is_paused' => 'boolean',
            'paused_at' => 'datetime',
            'resumed_at' => 'datetime',
        ];
    }

    /**
     * Get the user who paused the queue.
     */
    public function pausedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paused_by');
    }

    /**
     * Check if a queue is paused (global or specific)
     */
    public static function isQueuePaused(?string $queueName = null): bool
    {
        // Check global pause
        $globalPause = self::whereNull('queue_name')
            ->where('is_paused', true)
            ->exists();

        if ($globalPause) {
            return true;
        }

        // Check specific queue pause
        if ($queueName) {
            return self::where('queue_name', $queueName)
                ->where('is_paused', true)
                ->exists();
        }

        return false;
    }

    /**
     * Pause a queue
     */
    public static function pause(?string $queueName = null, ?string $reason = null, ?int $userId = null): self
    {
        $pause = self::firstOrNew(['queue_name' => $queueName]);
        $pause->is_paused = true;
        $pause->reason = $reason;
        $pause->paused_by = $userId;
        $pause->paused_at = now();
        $pause->resumed_at = null;
        $pause->save();

        return $pause;
    }

    /**
     * Resume a queue
     */
    public static function resume(?string $queueName = null): bool
    {
        $pause = self::where('queue_name', $queueName)->first();
        
        if ($pause && $pause->is_paused) {
            $pause->is_paused = false;
            $pause->resumed_at = now();
            $pause->save();
            return true;
        }

        return false;
    }
}

