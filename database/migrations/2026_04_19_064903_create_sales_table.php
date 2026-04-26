<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. ตรวจสอบว่ามีตาราง 'sales' หรือยัง
        if (!Schema::hasTable('sales')) {
            Schema::create('sales', function (Blueprint $table) {
                $table->id();
                $table->string('doc_no')->unique();
                $table->foreignId('customer_id');
                $table->integer('branch_id');
                $table->date('doc_date');
                $table->date('due_date');
                $table->decimal('subtotal', 15, 2);
                $table->decimal('vat', 15, 2);
                $table->decimal('total', 15, 2);
                $table->text('note')->nullable();
                $table->string('status')->default('ค้างชำระ');
                $table->timestamps();
            });
        } else {
            // 2. ถ้ามีตารางแล้ว แต่ต้องการเช็คฟิลด์ที่อาจจะยังไม่มี (ตัวอย่างการดักฟิลด์)
            Schema::table('sales', function (Blueprint $table) {
                if (!Schema::hasColumn('sales', 'tax_id')) {
                    // $table->string('tax_id')->nullable()->after('doc_no'); // ตัวอย่างการเพิ่มฟิลด์ใหม่
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
        if (Schema::hasTable('sales')) {
            Schema::dropIfExists('sales');
        }
    }
}
