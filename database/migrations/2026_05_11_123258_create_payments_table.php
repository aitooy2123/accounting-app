<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
     Schema::create('payments', function (Blueprint $table) {
        $table->id();
        $table->date('pay_date');
        $table->string('doc_no')->unique(); // เพิ่ม unique กันเลขที่ซ้ำ
        $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
        $table->string('remark')->nullable(); // สำหรับใส่คำอธิบายรายการในสมุดรายวัน
        $table->decimal('subtotal', 15, 2); // ยอดก่อนภาษี
        $table->decimal('vat', 15, 2)->default(0); // ภาษีมูลค่าเพิ่ม
        $table->decimal('amount', 15, 2); // ยอดรวมสุทธิ (Net)
        $table->string('status')->default('active'); // สำหรับจัดการการยกเลิกรายการ
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
