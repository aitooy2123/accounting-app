<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


 class Expense extends Model
{
    // เพิ่มฟิลด์ที่หายไปลงใน array นี้
protected $fillable = [
    'expense_date',
    'payee_id',
    'description',
'total_amount',
    'vat_rate', // ← ต้องมี
    'status',
    'account_id',
    'remark',
];
    // เชื่อมไปยังผังบัญชี (Debit)
    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    // เชื่อมไปยังผู้รับเงิน
    public function payee()
    {
        return $this->belongsTo(Payee::class, 'payee_id');
    }

    // เชื่อมไปยังผู้สร้างรายการ
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
