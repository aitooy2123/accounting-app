<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentVoucher extends Model
{
    use HasFactory;

    protected $table = 'payment_vouchers';

    protected $fillable = [
        'pv_no',
        'pv_date',
        'payee_id',
        'note',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'pv_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    // =========================
    // RELATION : ITEMS
    // =========================

    public function items()
    {
        return $this->hasMany(
            PaymentVoucherItem::class,
            'payment_voucher_id'
        );
    }

    // =========================
    // RELATION : PAYEE
    // =========================

    public function payee()
    {
        return $this->belongsTo(
            Payee::class,
            'payee_id'
        );
    }
}
