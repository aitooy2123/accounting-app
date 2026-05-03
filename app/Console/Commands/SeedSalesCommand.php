<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sale;

class SeedSalesCommand extends Command
{
    protected $signature = 'seed:sales
                            {--count=100 : จำนวนรายการที่ต้องการ}
                            {--fresh : ลบข้อมูลเดิมก่อนเพิ่ม}';

    protected $description = 'เพิ่มข้อมูลการขายตัวอย่าง 100 รายการ';

    public function handle(): void
    {
        if ($this->option('fresh')) {
            if ($this->confirm('⚠️ ต้องการลบข้อมูลการขายทั้งหมดก่อนเพิ่มใหม่ ใช่หรือไม่?')) {
                Sale::truncate();
                $this->info('ลบข้อมูลการขายเดิมทั้งหมดแล้ว');
            }
        }

        $this->info("🚀 กำลังเพิ่มข้อมูลการขาย {$this->option('count')} รายการ...");

        $this->call('db:seed', ['--class' => 'SaleSeeder']);

        $this->newLine();
        $this->info('✅ เสร็จสิ้น!');

        $this->table(
            ['รายการ', 'จำนวน', 'มูลค่า'],
            [
                ['ทั้งหมด', Sale::count(), '฿ ' . number_format(Sale::sum('total'), 2)],
                ['ชำระแล้ว', Sale::where('status', 'ชำระแล้ว')->count(), '฿ ' . number_format(Sale::where('status', 'ชำระแล้ว')->sum('total'), 2)],
                ['ค้างชำระ', Sale::where('status', 'ค้างชำระ')->count(), '฿ ' . number_format(Sale::where('status', 'ค้างชำระ')->sum('total'), 2)],
            ]
        );
    }
}
