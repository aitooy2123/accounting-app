<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('purchase_items', function (Blueprint $table) {
    $table->id();
    // เชื่อมไปยังตาราง purchases (ถ้าลบใบสั่งซื้อ รายการนี้จะถูกลบด้วย)
    $table->foreignId('purchase_id')->constrained()->onDelete('cascade');
    $table->string('desc');          // รายละเอียดสินค้า
    $table->decimal('qty', 15, 2);   // จำนวน
    $table->decimal('price', 15, 2); // ราคาต่อหน่วย
    $table->decimal('total', 15, 2); // ราคารวมของบรรทัดนั้น
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
        Schema::dropIfExists('purchase_items');
    }
}
