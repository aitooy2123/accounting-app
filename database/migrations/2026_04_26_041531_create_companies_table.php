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
        // 1. ตรวจสอบและสร้างตาราง (Has Table)
        if (!Schema::hasTable('companies')) {
            Schema::create('companies', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('address')->nullable();
                $table->string('type')->nullable();
                $table->string('tax_id')->nullable();
                $table->timestamps();
            });
        } else {
            // 2. ถ้ามีตารางอยู่แล้ว ให้ตรวจสอบและเพิ่มฟิลด์ที่ขาด (Has Column)
            Schema::table('companies', function (Blueprint $table) {
                if (!Schema::hasColumn('companies', 'tax_id')) {
                    $table->string('tax_id')->nullable()->after('type');
                }

                // คุณสามารถเพิ่มการเช็คฟิลด์อื่นๆ ได้ที่นี่
                if (!Schema::hasColumn('companies', 'website')) {
                    $table->string('website')->nullable()->after('email');
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
