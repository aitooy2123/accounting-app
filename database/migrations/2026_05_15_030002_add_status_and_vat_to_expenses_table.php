<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            // ตรวจสอบก่อนว่าตาราง expenses มีอยู่จริงไหม
            if (Schema::hasTable('expenses')) {
                Schema::table('expenses', function (Blueprint $table) {

                    // ดัก Error: ตรวจสอบว่าคอลัมน์ status มีอยู่หรือยังก่อนจะสร้าง
                    if (!Schema::hasColumn('expenses', 'status')) {
                        $table->string('status')
                              ->default('paid')
                              ->comment('paid, pending, invoiced')
                              ->after('remark');
                    }

                    // ดัก Error: ตรวจสอบว่าคอลัมน์ vat_rate มีอยู่หรือยังก่อนจะสร้าง
                    if (!Schema::hasColumn('expenses', 'vat_rate')) {
                        $table->integer('vat_rate')
                              ->default(0)
                              ->after('amount');
                    }
                });
            } else {
                Log::error("Migration Error: Table 'expenses' not found.");
            }
        } catch (\Exception $e) {
            // บันทึก Error ลง Log หากเกิดข้อผิดพลาดทางเทคนิค
            Log::error("Migration failed: " . $e->getMessage());
            throw $e; // ปล่อย Exception ออกไปเพื่อให้ Artisan แจ้งเตือนที่หน้าจอด้วย
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            if (Schema::hasTable('expenses')) {
                Schema::table('expenses', function (Blueprint $table) {
                    $columns = [];

                    if (Schema::hasColumn('expenses', 'status')) {
                        $columns[] = 'status';
                    }

                    if (Schema::hasColumn('expenses', 'vat_rate')) {
                        $columns[] = 'vat_rate';
                    }

                    if (!empty($columns)) {
                        $table->dropColumn($columns);
                    }
                });
            }
        } catch (\Exception $e) {
            Log::error("Rollback failed: " . $e->getMessage());
        }
    }
};
