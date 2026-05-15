<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'doc_no',
        'expense_date',
        'payee_id',
        'account_id', // ผังบัญชี (Debit)
        'payment_id', // ช่องทางเงินสด/ธนาคาร (Credit) - ถ้ามี
        'branch_id',
        'amount',
        'description',
        'remark',
        'status',
        'created_by',
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
