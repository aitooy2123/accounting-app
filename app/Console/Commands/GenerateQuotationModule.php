<?php

namespace App\Console\Commands;

use App\Models\Quotation;
use Illuminate\Console\Command;
use Carbon\Carbon;

class QuotationManagerCommand extends Command
{
    /**
     * ชื่อและคำอธิบายของคำสั่ง
     */
    protected $signature = 'quotation:manage
                            {action? : การกระทำที่ต้องการ (expire, cleanup, stats, generate, convert-overdue)}
                            {--id= : รหัสใบเสนอราคา (ใช้กับ generate, convert)}
                            {--days=15 : จำนวนวันสำหรับตรวจสอบ (ใช้กับ expire, cleanup)}
                            {--customer= : รหัสลูกค้า (ใช้กับ generate)}
                            {--force : บังคับดำเนินการโดยไม่ต้องยืนยัน}';

    protected $description = 'คำสั่งจัดการใบเสนอราคาต่างๆ (Quotation Management)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $action = $this->argument('action') ?? $this->choice(
            'เลือกการกระทำที่ต้องการ',
            ['expire', 'cleanup', 'stats', 'generate'],
            0
        );

        return match ($action) {
            'expire' => $this->expireQuotations(),
            'cleanup' => $this->cleanupQuotations(),
            'stats' => $this->showStatistics(),
            'generate' => $this->generateQuotationNumber(),
            default => $this->showHelp(),
        };
    }

    /**
     * ตรวจสอบและอัปเดตสถานะใบเสนอราคาที่หมดอายุ
     */
    protected function expireQuotations(): int
    {
        $days = $this->option('days');

        $this->info("⏳ กำลังตรวจสอบใบเสนอราคาที่หมดอายุ (เกิน {$days} วัน)...");

        // ค้นหาใบเสนอราคาที่หมดอายุและยังเป็นสถานะ draft หรือ sent
        $expiredQuotations = Quotation::whereIn('status', ['draft', 'sent'])
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', Carbon::now()->subDays($days))
            ->get();

        if ($expiredQuotations->isEmpty()) {
            $this->info('✅ ไม่พบใบเสนอราคาที่หมดอายุ');
            return self::SUCCESS;
        }

        $this->table(
            ['เลขที่เอกสาร', 'วันที่ออก', 'วันหมดอายุ', 'สถานะปัจจุบัน', 'ลูกค้า', 'ยอดรวม'],
            $expiredQuotations->map(fn ($q) => [
                $q->quotation_number,
                $q->quotation_date->format('d/m/Y'),
                $q->expiry_date->format('d/m/Y'),
                $q->status_label,
                $q->buyer_company_name,
                number_format($q->grand_total, 2),
            ])
        );

        if (!$this->option('force') && !$this->confirm('ต้องการอัปเดตสถานะเป็น "หมดอายุ" หรือไม่?', true)) {
            $this->info('⏭️ ยกเลิกการดำเนินการ');
            return self::SUCCESS;
        }

        $count = 0;
        foreach ($expiredQuotations as $quotation) {
            $quotation->update(['status' => Quotation::STATUS_EXPIRED]);
            $count++;
        }

        $this->info("✅ อัปเดตสถานะใบเสนอราคาที่หมดอายุเรียบร้อยแล้ว {$count} รายการ");

        return self::SUCCESS;
    }

    /**
     * ล้างข้อมูลใบเสนอราคาเก่าที่ยกเลิกหรือหมดอายุ
     */
    protected function cleanupQuotations(): int
    {
        $days = $this->option('days');

        $this->warn("⚠️  กำลังค้นหาใบเสนอราคาที่จะลบ (หมดอายุ/ยกเลิก เกิน {$days} วัน)...");

        $deletableQuotations = Quotation::whereIn('status', ['expired', 'cancelled', 'rejected'])
            ->where('updated_at', '<', Carbon::now()->subDays($days))
            ->get();

        if ($deletableQuotations->isEmpty()) {
            $this->info('✅ ไม่พบใบเสนอราคาที่ต้องลบ');
            return self::SUCCESS;
        }

        $this->table(
            ['เลขที่เอกสาร', 'สถานะ', 'อัปเดตล่าสุด', 'ลูกค้า', 'ยอดรวม'],
            $deletableQuotations->map(fn ($q) => [
                $q->quotation_number,
                $q->status_label,
                $q->updated_at->format('d/m/Y H:i'),
                $q->buyer_company_name,
                number_format($q->grand_total, 2),
            ])
        );

        if (!$this->option('force') && !$this->confirm('⚠️  ต้องการลบใบเสนอราคาเหล่านี้หรือไม่? (ไม่สามารถกู้คืนได้)', false)) {
            $this->info('⏭️  ยกเลิกการดำเนินการ');
            return self::SUCCESS;
        }

        $count = 0;
        foreach ($deletableQuotations as $quotation) {
            $quotation->forceDelete(); // ลบถาวร
            $count++;
        }

        $this->info("✅ ลบใบเสนอราคาเรียบร้อยแล้ว {$count} รายการ");

        return self::SUCCESS;
    }

    /**
     * แสดงสถิติใบเสนอราคา
     */
    protected function showStatistics(): int
    {
        $this->info('📊 สถิติใบเสนอราคา');
        $this->newLine();

        // สถิติตามสถานะ
        $statusCounts = Quotation::selectRaw('status, COUNT(*) as count, SUM(grand_total) as total_amount')
            ->groupBy('status')
            ->get();

        $this->line('📋 แยกตามสถานะ:');
        $this->table(
            ['สถานะ', 'จำนวน', 'มูลค่ารวม'],
            $statusCounts->map(fn ($s) => [
                Quotation::getStatuses()[$s->status] ?? $s->status,
                number_format($s->count),
                number_format($s->total_amount, 2),
            ])
        );

        // สถิติรายเดือน (ปีปัจจุบัน)
        $monthlyStats = Quotation::selectRaw("DATE_FORMAT(quotation_date, '%Y-%m') as month, COUNT(*) as count, SUM(grand_total) as total_amount")
            ->whereYear('quotation_date', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $this->newLine();
        $this->line('📅 แยกรายเดือน (ปี ' . (now()->year + 543) . '):');
        $this->table(
            ['เดือน', 'จำนวน', 'มูลค่ารวม'],
            $monthlyStats->map(fn ($s) => [
                Carbon::createFromFormat('Y-m', $s->month)->locale('th')->monthName,
                number_format($s->count),
                number_format($s->total_amount, 2),
            ])
        );

        // สรุปภาพรวม
        $this->newLine();
        $allTime = Quotation::selectRaw('COUNT(*) as total_count, SUM(grand_total) as total_amount')->first();
        $conversionRate = Quotation::where('status', 'converted')->count();
        $totalApproved = Quotation::where('status', 'approved')->count();

        $this->line('📈 ภาพรวมทั้งหมด:');
        $this->table(
            ['รายการ', 'ข้อมูล'],
            [
                ['จำนวนใบเสนอราคาทั้งหมด', number_format($allTime->total_count) . ' รายการ'],
                ['มูลค่ารวมทั้งหมด', number_format($allTime->total_amount, 2) . ' บาท'],
                ['จำนวนที่อนุมัติ', number_format($totalApproved) . ' รายการ'],
                ['จำนวนที่แปลงเป็นใบแจ้งหนี้', number_format($conversionRate) . ' รายการ'],
            ]
        );

        return self::SUCCESS;
    }

    /**
     * สร้างเลขที่ใบเสนอราคาใหม่ (สำหรับทดสอบ)
     */
    protected function generateQuotationNumber(): int
    {
        $quotationNumber = Quotation::generateQuotationNumber();

        $this->info('🔢 เลขที่ใบเสนอราคาล่าสุดที่สร้างได้:');
        $this->newLine();
        $this->line("    <fg=green;options=bold>{$quotationNumber}</>");
        $this->newLine();

        // แสดงรูปแบบการสร้าง
        $this->line('📝 รูปแบบ: QT-<ปี พ.ศ.>-<รันนิง 4 หลัก>');
        $this->line('📌 ตัวอย่าง: QT-2567-0001, QT-2567-0002, ...');

        return self::SUCCESS;
    }

    /**
     * แสดงคำอธิบายคำสั่ง
     */
    protected function showHelp(): int
    {
        $this->info('📖 คำสั่งจัดการใบเสนอราคา (Quotation Manager)');
        $this->newLine();

        $this->line('การใช้งาน:');
        $this->line('  php artisan quotation:manage <action> [options]');
        $this->newLine();

        $this->line('Actions:');
        $this->line('  <fg=cyan>expire</>      ตรวจสอบและอัปเดตใบเสนอราคาที่หมดอายุ');
        $this->line('  <fg=cyan>cleanup</>     ลบใบเสนอราคาเก่าที่ยกเลิก/หมดอายุ');
        $this->line('  <fg=cyan>stats</>       แสดงสถิติใบเสนอราคา');
        $this->line('  <fg=cyan>generate</>    สร้างเลขที่ใบเสนอราคาใหม่ (ทดสอบ)');
        $this->newLine();

        $this->line('Options:');
        $this->line('  <fg=yellow>--id=</>         รหัสใบเสนอราคา');
        $this->line('  <fg=yellow>--days=</>       จำนวนวัน (ค่าเริ่มต้น: 15)');
        $this->line('  <fg=yellow>--force</>       บังคับดำเนินการโดยไม่ต้องยืนยัน');
        $this->newLine();

        $this->line('ตัวอย่าง:');
        $this->line('  php artisan quotation:manage expire --days=30');
        $this->line('  php artisan quotation:manage cleanup --force');
        $this->line('  php artisan quotation:manage stats');
        $this->line('  php artisan quotation:manage generate');

        return self::SUCCESS;
    }
}
