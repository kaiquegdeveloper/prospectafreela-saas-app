<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'monthly_prospect_quota',
        'daily_prospect_quota',
        'price',
        'is_active',
    ];

    /**
     * Users that belong to this plan.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}


