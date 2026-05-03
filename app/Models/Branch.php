<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'address',
        'phone',
        'email',
        'manager',
        'is_active',
        'copany_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
