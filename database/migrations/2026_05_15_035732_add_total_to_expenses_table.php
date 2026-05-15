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
            Schema::table('expenses', function (Blueprint $table) {
                // เพิ่มเฉพาะคอลัมน์ total
                if (!Schema::hasColumn('expenses', 'total')) {
                    $table->decimal('total', 15, 2)->after('amount')->default(0);
                }
            });
        } catch (\Exception $e) {
            // บันทึก Error ลง Log เพื่อให้ตรวจสอบได้ภายหลัง
            Log::error("Migration Error (up): " . $e->getMessage());

            // พ่น Error ออกมาเพื่อให้หน้า Terminal แจ้งเตือน
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('expenses', function (Blueprint $table) {
                if (Schema::hasColumn('expenses', 'total')) {
                    $table->dropColumn('total');
                }
            });
        } catch (\Exception $e) {
            Log::error("Migration Error (down): " . $e->getMessage());
            throw $e;
        }
    }
};
