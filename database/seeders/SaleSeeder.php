<?php

namespace Database\Seeders;

use App\Models\Sale;
use App\Models\Customer;
use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = Customer::all();
        $branches = Branch::all();

        if ($customers->isEmpty()) {
            $this->command->error('❌ ไม่พบข้อมูลลูกค้า กรุณารัน CustomerSeeder ก่อน');
            return;
        }

        if ($branches->isEmpty()) {
            $this->command->error('❌ ไม่พบข้อมูลสาขา กรุณารัน BranchSeeder ก่อน');
            return;
        }

        $statuses = ['ชำระแล้ว', 'ค้างชำระ', 'ค้างชำระ', 'ค้างชำระ', 'ชำระแล้ว']; // 40% paid, 60% unpaid
        $notes = [
            'จัดส่งด่วน',
            'รอตรวจสอบ',
            'จัดส่งภายใน 3 วัน',
            null,
            'ลูกค้าขอส่วนลดเพิ่ม',
            'จัดส่งตามปกติ',
            'ใบสั่งซื้อประจำเดือน',
            'โปรเจคพิเศษ',
            null,
            'ขายเชื่อ 30 วัน',
            'สั่งซื้อเร่งด่วน',
            null,
        ];

        $sales = [];
        $docPrefix = 'INV-' . date('Ym') . '-';
        $startDate = Carbon::now()->subMonths(6);
        $endDate = Carbon::now();

        for ($i = 1; $i <= 100; $i++) {
            $customer = $customers->random();
            $branch = $branches->random();

            // สุ่มวันที่ในช่วง 6 เดือนที่ผ่านมา
            $docDate = Carbon::createFromTimestamp(rand($startDate->timestamp, $endDate->timestamp));
            $dueDate = (clone $docDate)->addDays(rand(15, 60));

            // สุ่มยอดเงิน
            $subtotal = rand(1000, 200000) + (rand(0, 99) / 100);
            $vatRate = 7.00;
            $vat = round($subtotal * ($vatRate / 100), 2);
            $total = $subtotal + $vat;

            // สุ่มสถานะ (ปลายงวดมีโอกาสค้างชำระน้อยกว่า)
            $statusIndex = rand(0, 4);
            if ($docDate->diffInDays(Carbon::now()) > 90) {
                $statusIndex = rand(0, 1); // เก่าแล้ว มีโอกาสชำระแล้วมากกว่า
            }

            $sales[] = [
                'doc_no' => $docPrefix . str_pad($i, 4, '0', STR_PAD_LEFT),
                'customer_id' => $customer->id,
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
            ];
        }

        // เรียงตามวันที่
        usort($sales, function ($a, $b) {
            return strcmp($a['doc_date'], $b['doc_date']);
        });

        // อัปเดต doc_no ให้เรียงตามวันที่
        foreach ($sales as $index => &$sale) {
            $saleOrder = $index + 1;
            $sale['doc_no'] = $docPrefix . str_pad($saleOrder, 4, '0', STR_PAD_LEFT);
        }

        // Batch insert
        foreach (array_chunk($sales, 50) as $chunk) {
            Sale::insert($chunk);
        }

        $this->command->info('✅ SaleSeeder: เพิ่มข้อมูลการขาย ' . count($sales) . ' รายการเรียบร้อยแล้ว');

        // แสดงสรุป
        $this->command->table(
            ['รายการ', 'จำนวน', 'มูลค่ารวม'],
            [
                ['ทั้งหมด', Sale::count(), '฿ ' . number_format(Sale::sum('total'), 2)],
                ['ชำระแล้ว', Sale::where('status', 'ชำระแล้ว')->count(), '฿ ' . number_format(Sale::where('status', 'ชำระแล้ว')->sum('total'), 2)],
                ['ค้างชำระ', Sale::where('status', 'ค้างชำระ')->count(), '฿ ' . number_format(Sale::where('status', 'ค้างชำระ')->sum('total'), 2)],
            ]
        );

        // แสดงยอดรวมรายเดือน
        $this->command->newLine();
        $this->command->info('📊 ยอดขายรายเดือน:');

        $monthlySales = Sale::selectRaw('DATE_FORMAT(doc_date, "%Y-%m") as month, COUNT(*) as count, SUM(total) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        foreach ($monthlySales as $month) {
            $this->command->line("  • {$month->month}: {$month->count} รายการ | ฿ " . number_format($month->total, 2));
        }
    }
}
