<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessageTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'channel',
        'name',
        'content',
        'is_active',
    ];
}


