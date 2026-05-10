<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. ตรวจสอบและสร้างตาราง (ถ้ายังไม่มี)
        if (!Schema::hasTable('companies')) {
            Schema::create('companies', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique(); // รหัสบริษัท auto generate
                $table->string('name')->unique();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->text('address')->nullable();
                $table->string('tax_id')->nullable();
                $table->string('type')->nullable();
                $table->boolean('is_active')->default(true); // สถานะการใช้งาน
                $table->timestamps();

                // เพิ่ม index เพื่อการค้นหาที่เร็วขึ้น
                $table->index('code');
                $table->index('is_active');
            });
        } else {
            // 2. ถ้ามีตารางอยู่แล้ว ให้ตรวจสอบและเพิ่มฟิลด์ที่ขาด
            Schema::table('companies', function (Blueprint $table) {
                // เพิ่มฟิลด์ code ถ้ายังไม่มี
                if (!Schema::hasColumn('companies', 'code')) {
                    $table->string('code')->unique()->after('id');
                }

                // เพิ่มฟิลด์ is_active ถ้ายังไม่มี
                if (!Schema::hasColumn('companies', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('tax_id');
                }

                // ตรวจสอบและเพิ่มฟิลด์ tax_id ถ้ายังไม่มี
                if (!Schema::hasColumn('companies', 'tax_id')) {
                    $table->string('tax_id')->nullable()->after('address');
                }

                // เพิ่ม index สำหรับ code และ is_active
                if (!Schema::hasIndex('companies', 'companies_code_index')) {
                    $table->index('code');
                }

                if (!Schema::hasIndex('companies', 'companies_is_active_index')) {
                    $table->index('is_active');
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
        if (Schema::hasTable('companies')) {
            Schema::dropIfExists('companies');
        }
    }
}
