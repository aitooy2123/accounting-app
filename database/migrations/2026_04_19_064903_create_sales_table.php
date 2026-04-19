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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('doc_no')->unique(); // เลขที่ใบกำกับ
            $table->foreignId('customer_id');   // เชื่อมโยงลูกค้า
            $table->integer('branch_id');       // สาขา
            $table->date('doc_date');           // วันที่เอกสาร
            $table->date('due_date');           // วันครบกำหนด
            $table->decimal('subtotal', 15, 2); // ยอดก่อนภาษี
            $table->decimal('vat', 15, 2);      // ภาษี 7%
            $table->decimal('total', 15, 2);    // ยอดสุทธิ
            $table->text('note')->nullable();   // หมายเหตุ
            $table->string('status')->default('ค้างชำระ');
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
        Schema::dropIfExists('sales');
    }
}
