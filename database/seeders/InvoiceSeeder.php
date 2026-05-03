<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::all();
        $branches = Branch::all();

        if ($customers->isEmpty() || $branches->isEmpty()) {
            $this->command->error('❌ ต้องมี Customer และ Branch ก่อน');
            return;
        }

        $created = 0;
        $updated = 0;
        $docPrefix = 'INV-' . date('Ym') . '-';

        for ($i = 1; $i <= 50; $i++) {
            $customer = $customers->random();
            $branch = $branches->random();

            // สุ่มวันที่ย้อนหลัง 30 วัน
            $docDate = Carbon::now()->subDays(rand(0, 30));
            $subtotal = rand(1000, 100000);
            $discount = rand(0, 5000);
            $tax = round(($subtotal - $discount) * 0.07, 2);
            $total = $subtotal - $discount + $tax;

            $docNo = $docPrefix . str_pad($i, 4, '0', STR_PAD_LEFT);

            $result = DB::table('invoices')->updateOrInsert(
                ['doc_no' => $docNo],
                [
                    'invoice_date' => $docDate->format('Y-m-d'),
                    'customer_id' => $customer->id,
                    'branch_id' => $branch->id,
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'tax' => $tax,
                    'total' => $total,
                    'status' => rand(0, 1) ? 'ชำระแล้ว' : 'ค้างชำระ',
                    'remark' => 'บันทึกอัตโนมัติ',
                    'created_by' => 1,
                    'created_at' => $docDate,
                    'updated_at' => $docDate,
                ]
            );

            $result ? $created++ : $updated++;
        }

        $this->command->info("✅ InvoiceSeeder: เพิ่ม {$created} รายการ, อัปเดต {$updated} รายการ");
    }
}
