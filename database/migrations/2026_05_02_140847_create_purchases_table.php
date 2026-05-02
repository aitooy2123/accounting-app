<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
        $table->id();
        $table->string('po_no')->unique(); // เลขที่ใบสั่งซื้อ
        $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade'); // เชื่อมกับลูกค้า
        $table->decimal('total_amount', 15, 2)->default(0); // ยอดรวม
        $table->string('status')->default('pending'); // สถานะ
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
        Schema::dropIfExists('purchases');
    }
}
