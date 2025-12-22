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
     * Formato esperado: 55XXXXXXXXXXX (DDI + DDD + número)
     */
    public function getWhatsappLinkNumberAttribute(): ?string
    {
        $number = $this->whatsapp ?: $this->telefone;

        if (!$number) {
            return null;
        }

        // Remove todos os caracteres não numéricos
        $digits = preg_replace('/\D/', '', $number);

        if ($digits === '' || strlen($digits) < 10) {
            return null;
        }

        // Se já começa com 55 e tem pelo menos 12 dígitos (55 + 10 ou 11 dígitos)
        if (str_starts_with($digits, '55') && strlen($digits) >= 12) {
            // Garante que tem exatamente 13 dígitos (55 + DDD + 9 dígitos) ou 12 (55 + DDD + 8 dígitos)
            if (strlen($digits) === 12 || strlen($digits) === 13) {
                return $digits;
            }
            // Se tem mais de 13, pega apenas os primeiros 13
            if (strlen($digits) > 13) {
                return substr($digits, 0, 13);
            }
        }

        // Remove o zero inicial se houver (formato antigo brasileiro)
        if (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        // Caso típico brasileiro: DDD (2 dígitos) + número (8 ou 9 dígitos)
        if (strlen($digits) === 10 || strlen($digits) === 11) {
            return '55' . $digits;
        }

        // Se tem 8 ou 9 dígitos sem DDD, assume DDD padrão (não ideal, mas melhor que nada)
        if (strlen($digits) === 8 || strlen($digits) === 9) {
            // Tenta extrair DDD do telefone original se possível
            // Por enquanto, retorna null pois não temos DDD
            return null;
        }

        // Fallback: se tem mais de 11 dígitos sem 55, pode ser formato internacional
        if (strlen($digits) > 11 && !str_starts_with($digits, '55')) {
            // Assume que já está no formato correto ou adiciona 55
            return '55' . substr($digits, -11); // Pega os últimos 11 dígitos
        }

        return null;
    }
}

