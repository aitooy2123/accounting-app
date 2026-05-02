<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $table = 'purchases';

    protected $fillable = [
        'purchase_no',
        'customer_id', // สำคัญมากสำหรับการเชื่อมโยง
        'total_amount',
        'status',
        // เพิ่ม field อื่นๆ ตามตารางใน DB ของคุณ
    ];

    /**
     * ความสัมพันธ์กลับไปที่ Customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
