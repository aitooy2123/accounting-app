<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payee extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
    ];

    public function paymentVouchers()
    {
        return $this->hasMany(
            PaymentVoucher::class,
            'payee_id'
        );
    }
}
