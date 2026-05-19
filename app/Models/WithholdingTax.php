<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithholdingTax extends Model
{
    protected $fillable = [
        'company_id', 'expense_id', 'withholding_no', 'withholding_date',
        'invoice_no', 'tax_base', 'tax_rate', 'tax_amount', 'remark'
    ];

    protected $casts = [
        'withholding_date' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }
}
