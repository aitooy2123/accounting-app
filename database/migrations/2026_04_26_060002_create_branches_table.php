<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // 1. ตรวจสอบและสร้างตาราง (Schema::hasTable)
        if (!Schema::hasTable('branches')) {
            Schema::create('branches', function (Blueprint $table) {
                $table->id();
                $table->string('code');              // รหัสสาขา
                $table->string('name');              // ชื่อสาขา
                $table->string('address')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('manager')->nullable(); // ผู้จัดการสาขา
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        } else {
            // 2. ถ้ามีตารางอยู่แล้ว ให้ตรวจสอบและเพิ่มคอลัมน์ที่ยังไม่มี (Schema::hasColumn)
            Schema::table('branches', function (Blueprint $table) {
                if (!Schema::hasColumn('branches', 'code')) {
                    $table->string('code')->after('id');
                }
                if (!Schema::hasColumn('branches', 'name')) {
                    $table->string('name')->after('code');
                }
                if (!Schema::hasColumn('branches', 'address')) {
                    $table->string('address')->nullable()->after('name');
                }
                if (!Schema::hasColumn('branches', 'phone')) {
                    $table->string('phone')->nullable()->after('address');
                }
                if (!Schema::hasColumn('branches', 'email')) {
                    $table->string('email')->nullable()->after('phone');
                }
                if (!Schema::hasColumn('branches', 'manager')) {
                    $table->string('manager')->nullable()->after('email');
                }
                if (!Schema::hasColumn('branches', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('manager');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // ลบตารางเฉพาะเมื่อมีตารางอยู่จริง
        if (Schema::hasTable('branches')) {
            Schema::dropIfExists('branches');
        }
    }
}
