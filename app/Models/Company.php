<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $table = 'companies'; // ตัวแปร
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'tax_id',
        'type'
    ];
}
