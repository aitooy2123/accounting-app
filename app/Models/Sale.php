<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;

    protected $table = 'sales'; // ระบุชื่อตาราง

    protected $fillable = [
        'doc_no',
        'customer_id',
        'branch_id',
        'doc_date',
        'due_date',
        'subtotal',
        'vat',
        'total',
        'note',
        'status',
    ];

    /**
     * กำหนดการแปลงประเภทข้อมูลอัตโนมัติ (Casting)
     */
    protected $casts = [
        'doc_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'vat'      => 'decimal:2',
        'total'    => 'decimal:2',
    ];

    /**
     * ความสัมพันธ์กับรายการสินค้า (Sale Items)
     */
    public function items()
    {
        return $this->hasMany(SaleItem::class, 'sale_id', 'id');
    }

    // หากคุณมี Model Customer สามารถทำ BelongsTo ได้
    // public function customer() {
    //     return $this->belongsTo(Customer::class);
    // }
}
