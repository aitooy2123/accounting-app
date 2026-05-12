<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentVoucher;
use App\Models\PaymentVoucherItem;

class PaymentVoucherSeeder extends Seeder
{
    public function run()
    {
        $pv = PaymentVoucher::create([
            'pv_no' => 'PV-20260512001',
            'pv_date' => now(),
            'note' => 'จ่ายค่าเช่า',
            'total_amount' => 10000,
        ]);

        PaymentVoucherItem::create([
            'payment_voucher_id' => $pv->id,
            'chart_of_account_id' => 5,
            'type' => 'dr',
            'amount' => 10000,
            'description' => 'ค่าเช่า'
        ]);

        PaymentVoucherItem::create([
            'payment_voucher_id' => $pv->id,
            'chart_of_account_id' => 1,
            'type' => 'cr',
            'amount' => 10000,
            'description' => 'เงินสด'
        ]);
    }
}
