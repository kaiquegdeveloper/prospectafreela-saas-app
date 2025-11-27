<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Prospect extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'nome',
        'telefone',
        'whatsapp',
        'email',
        'site',
        'endereco',
        'cidade',
        'nicho',
        'google_maps_url',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => 'string',
        ];
    }

    /**
     * Get the user that owns the prospect.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Lead information related to this prospect.
     */
    public function lead(): HasOne
    {
        return $this->hasOne(ProspectLead::class);
    }

    /**
     * Scope a query to only include prospects for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to search prospects.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nome', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('telefone', 'like', "%{$search}%")
                ->orWhere('cidade', 'like', "%{$search}%")
                ->orWhere('nicho', 'like', "%{$search}%");
        });
    }

    /**
     * Normalized WhatsApp number (for wa.me links).
     * Falls back to telefone if whatsapp is empty.
     */
    public function getWhatsappLinkNumberAttribute(): ?string
    {
        $number = $this->whatsapp ?: $this->telefone;

        if (!$number) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $number);

        if ($digits === '') {
            return null;
        }

        // Já vem com DDI 55
        if (str_starts_with($digits, '55')) {
            return $digits;
        }

        // Caso típico brasileiro: DDD + número (10 ou 11 dígitos)
        if (strlen($digits) === 10 || strlen($digits) === 11) {
            return '55' . $digits;
        }

        // Fallback: retorna o que foi possível normalizar
        return $digits;
    }
}

