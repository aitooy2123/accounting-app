<?php

namespace Database\Seeders;

use App\Models\Purchase;
use App\Models\Customer;
use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PurchaseSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = Customer::all();
        $branches = Branch::all();

        if ($suppliers->isEmpty()) {
            $this->command->error('❌ ไม่พบข้อมูลลูกค้า กรุณารัน CustomerSeeder ก่อน');
            return;
        }

        if ($branches->isEmpty()) {
            $this->command->error('❌ ไม่พบข้อมูลสาขา กรุณารัน BranchSeeder ก่อน');
            return;
        }

        $statuses = ['ชำระแล้ว', 'ค้างชำระ', 'ค้างชำระ', 'ค้างชำระ', 'ชำระแล้ว'];
        $notes = [
            'สั่งซื้อวัตถุดิบ',
            'ซื้ออุปกรณ์สำนักงาน',
            'ค่าบริการที่ปรึกษา',
            null,
            'ซื้อเครื่องจักร',
            'ค่าขนส่ง',
            'ซื้อสินค้าเข้า Stock',
            null,
            'ค่าซ่อมบำรุง',
            'ซื้อเชื่อ 30 วัน',
            null,
        ];

        $created = 0;
        $updated = 0;
        $docPrefix = 'PO-' . date('Ym') . '-';
        $startDate = Carbon::now()->subMonths(6);
        $endDate = Carbon::now();

        for ($i = 1; $i <= 50; $i++) {
            $supplier = $suppliers->random();
            $branch = $branches->random();

            $docDate = Carbon::createFromTimestamp(rand($startDate->timestamp, $endDate->timestamp));
            $dueDate = (clone $docDate)->addDays(rand(15, 60));

            $subtotal = rand(1000, 100000) + (rand(0, 99) / 100);
            $vatRate = 7.00;
            $vat = round($subtotal * ($vatRate / 100), 2);
            $total = $subtotal + $vat;

            $statusIndex = rand(0, 4);
            if ($docDate->diffInDays(Carbon::now()) > 90) {
                $statusIndex = rand(0, 1);
            }

            $docNo = $docPrefix . str_pad($i, 4, '0', STR_PAD_LEFT);

            $result = Purchase::updateOrCreate(
                ['doc_no' => $docNo],
                [
                    'supplier_id' => $supplier->id,
                    'branch_id' => $branch->id,
                    'doc_date' => $docDate->format('Y-m-d'),
                    'due_date' => $dueDate->format('Y-m-d'),
                    'subtotal' => $subtotal,
                    'vat' => $vat,
                    'total' => $total,
                    'vat_rate' => $vatRate,
                    'note' => $notes[array_rand($notes)],
                    'status' => $statuses[$statusIndex],
                    'created_at' => $docDate,
                    'updated_at' => $docDate,
                ]
            );

            if ($result->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        $this->command->info("✅ PurchaseSeeder: เพิ่ม {$created} รายการ, อัปเดต {$updated} รายการ");

        $this->command->table(
            ['รายการ', 'จำนวน', 'มูลค่ารวม'],
            [
                ['ทั้งหมด', Purchase::count(), '฿ ' . number_format(Purchase::sum('total'), 2)],
                ['ชำระแล้ว', Purchase::paid()->count(), '฿ ' . number_format(Purchase::paid()->sum('total'), 2)],
                ['ค้างชำระ', Purchase::unpaid()->count(), '฿ ' . number_format(Purchase::unpaid()->sum('total'), 2)],
            ]
        );
    }
}
