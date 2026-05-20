<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithholdingTax extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_id', 'withholding_number', 'date', 'invoice_number',
        'amount_before_withholding', 'withholding_rate', 'withholding_amount', 'remark'
    ];

    protected $casts = [
        'date' => 'date',
    ];

 
    public function expense()
{
    return $this->belongsTo(Expense::class);
}

// ถ้าต้องการเรียก $withholdingTax->company โดยตรง
public function getCompanyAttribute()
{
    return $this->expense?->company;
}
}
