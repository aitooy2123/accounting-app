<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SeedPurchasesCommand extends Command
{
    protected $signature = 'seed:purchases {--fresh : ลบข้อมูลเดิมก่อนเพิ่ม}';
    protected $description = 'เพิ่มข้อมูลเอกสารซื้อตัวอย่าง 50 รายการ';

    public function handle(): void
    {
        if ($this->option('fresh')) {
            if ($this->confirm('ลบข้อมูลเอกสารซื้อทั้งหมด?')) {
                \App\Models\Purchase::truncate();
            }
        }

        $this->call('db:seed', ['--class' => 'PurchaseSeeder']);
        $this->info('✅ เสร็จสิ้น!');
    }
}
