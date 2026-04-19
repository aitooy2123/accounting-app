<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleItem extends Model
{
    use HasFactory;

    protected $table = 'sale_items';

    // กำหนดฟิลด์ที่อนุญาตให้บันทึกข้อมูลแบบ Mass Assignment
    protected $fillable = [
        'sale_id',
        'description',
        'quantity',
        'unit_price',
        'total',
    ];

    /**
     * การแปลงประเภทข้อมูล (Casting)
     */
    protected $casts = [
        'quantity'   => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total'      => 'decimal:2',
    ];

    /**
     * ความสัมพันธ์ย้อนกลับไปยังหัวเอกสาร (Sale)
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id', 'id');
    }
}
