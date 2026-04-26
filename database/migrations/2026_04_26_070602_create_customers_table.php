<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // 1. ตรวจสอบและสร้างตารางหากยังไม่มี
        if (!Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();          // รหัสลูกค้า
                $table->string('name');                    // ชื่อลูกค้า/บริษัท
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->text('address')->nullable();
                $table->string('tax_id')->nullable();       // เลขผู้เสียภาษี
                $table->string('contact_person')->nullable();
                $table->string('contact_phone')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        } else {
            // 2. ถ้ามีตารางอยู่แล้ว ให้ตรวจสอบและเพิ่มคอลัมน์ที่ยังไม่มีทีละฟิลด์
            Schema::table('customers', function (Blueprint $table) {
                if (!Schema::hasColumn('customers', 'code')) {
                    $table->string('code')->unique()->after('id');
                }
                if (!Schema::hasColumn('customers', 'name')) {
                    $table->string('name')->after('code');
                }
                if (!Schema::hasColumn('customers', 'email')) {
                    $table->string('email')->nullable()->after('name');
                }
                if (!Schema::hasColumn('customers', 'phone')) {
                    $table->string('phone')->nullable()->after('email');
                }
                if (!Schema::hasColumn('customers', 'address')) {
                    $table->text('address')->nullable()->after('phone');
                }
                if (!Schema::hasColumn('customers', 'tax_id')) {
                    $table->string('tax_id')->nullable()->after('address');
                }
                if (!Schema::hasColumn('customers', 'contact_person')) {
                    $table->string('contact_person')->nullable()->after('tax_id');
                }
                if (!Schema::hasColumn('customers', 'contact_phone')) {
                    $table->string('contact_phone')->nullable()->after('contact_person');
                }
                if (!Schema::hasColumn('customers', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('contact_phone');
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
        // ลบตารางเฉพาะเมื่อมีตารางอยู่จริงเท่านั้น
        if (Schema::hasTable('customers')) {
            Schema::dropIfExists('customers');
        }
    }
}
