<?php

namespace App\Models; // <--- ต้องเป็น App\Models

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    // หากชื่อตารางใน DB ไม่ได้ชื่อ payments ให้ระบุตรงนี้
    // protected $table = 'your_table_name';
}
