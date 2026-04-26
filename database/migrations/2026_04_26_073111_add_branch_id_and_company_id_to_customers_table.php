<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. ตรวจสอบว่ามีตาราง 'customers' อยู่จริงหรือไม่
        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table) {

                // 2. ดัก Error: ตรวจสอบและสร้างฟิลด์ 'branch_id'
                if (!Schema::hasColumn('customers', 'branch_id')) {
                    $table->foreignId('branch_id')
                          ->nullable()
                          ->after('id') // จัดวางตำแหน่งให้ดูง่าย
                          ->constrained('branches')
                          ->nullOnDelete();
                }

                // 3. ดัก Error: ตรวจสอบและสร้างฟิลด์ 'company_id'
                if (!Schema::hasColumn('customers', 'company_id')) {
                    $table->foreignId('company_id')
                          ->nullable()
                          ->after('branch_id')
                          ->constrained('companies')
                          ->nullOnDelete();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table) {
                // ตรวจสอบก่อนลบ Foreign Key เพื่อป้องกัน Error
                if (Schema::hasColumn('customers', 'branch_id')) {
                    $table->dropForeign(['branch_id']);
                }

                if (Schema::hasColumn('customers', 'company_id')) {
                    $table->dropForeign(['company_id']);
                }

                // ลบคอลัมน์ออก
                $table->dropColumn(['branch_id', 'company_id']);
            });
        }
    }
};
