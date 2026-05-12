<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentVoucherItem extends Model
{
    protected $fillable = [
        'payment_voucher_id',
        'chart_of_account_id',
        'type',
        'amount',
        'description'
    ];

    public function voucher()
    {
        return $this->belongsTo(PaymentVoucher::class);
    }
    public function account()
{
    return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
}
}
