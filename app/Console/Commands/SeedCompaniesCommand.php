<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\CompanySeeder;

class SeedCompaniesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:companies
                            {--fresh : ลบข้อมูลเดิมก่อนเพิ่ม}
                            {--count=20 : จำนวนรายการที่ต้องการ (ใช้กับ --random)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'เพิ่มข้อมูลบริษัทตัวอย่าง 20 รายการเข้าสู่ระบบ';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('🚀 กำลังเพิ่มข้อมูลบริษัทตัวอย่าง...');
        $this->newLine();

        // ถ้าใช้ --fresh จะลบข้อมูลเดิมก่อน
        if ($this->option('fresh')) {
            if ($this->confirm('⚠️ คุณต้องการลบข้อมูลบริษัททั้งหมดก่อนเพิ่มข้อมูลใหม่ ใช่หรือไม่?')) {
                $this->call('db:seed', ['--class' => 'CompanySeeder']);
                $this->info('✅ ลบข้อมูลเดิมและเพิ่มข้อมูลใหม่เรียบร้อยแล้ว');
            } else {
                $this->info('❌ ยกเลิกการดำเนินการ');
                return;
            }
        } else {
            // เพิ่มข้อมูลอย่างเดียว
            $this->call('db:seed', ['--class' => 'CompanySeeder']);
            $this->info('✅ เพิ่มข้อมูลบริษัทตัวอย่างเรียบร้อยแล้ว');
        }

        $this->newLine();
        $this->table(
            ['รายการ', 'จำนวน'],
            [
                ['บริษัททั้งหมด', \App\Models\Company::count()],
                ['เปิดใช้งาน', \App\Models\Company::where('is_active', true)->count()],
                ['ปิดใช้งาน', \App\Models\Company::where('is_active', false)->count()],
            ]
        );

        $this->newLine();
        $this->info('🎉 เสร็จสิ้น!');
    }
}
