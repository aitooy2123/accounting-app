<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentVoucher extends Model
{
    protected $fillable = [
        'pv_no',
        'pv_date',
        'payee_id',
        'note',
        'total_amount'
    ];

    public function items()
    {
        return $this->hasMany(PaymentVoucherItem::class);
    }
}
