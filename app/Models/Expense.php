<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


 class Expense extends Model
{
    // เพิ่มฟิลด์ที่หายไปลงใน array นี้
protected $fillable = [
    'doc_no', 'expense_date', 'payee_id', 'account_id',
    'amount', 'vat_rate', 'total_amount', 'description',
    'remark', 'status', 'created_by'
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
    protected $casts = [
    'expense_date' => 'date',
    // หรือ 'datetime'
];
}
