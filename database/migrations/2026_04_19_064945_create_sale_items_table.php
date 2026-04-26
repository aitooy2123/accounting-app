<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleItemsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. เช็คก่อนว่ามีตารางแม่ (sales) อยู่จริงไหม ถ้าไม่มีจะสร้าง FK ไม่ได้
        if (!Schema::hasTable('sales')) {
            // คุณสามารถเลือกให้หยุดทำงาน หรือ Throw Exception เพื่อบอกให้ไปรัน sales ก่อน
            return;
        }

        // 2. เช็คว่ามีตาราง sale_items หรือยัง
        if (!Schema::hasTable('sale_items')) {
            Schema::create('sale_items', function (Blueprint $table) {
                $table->id();

                // การดัก Error: ใช้ constrained() อ้างอิงตาราง sales
                $table->foreignId('sale_id')
                      ->constrained('sales')
                      ->onDelete('cascade');

                $table->string('description');
                $table->decimal('quantity', 10, 2);
                $table->decimal('unit_price', 15, 2);
                $table->decimal('total', 15, 2);
                $table->timestamps();
            });
        } else {
            // 3. ถ้ามีตารางแล้ว แต่ต้องการเช็คฟิลด์เผื่อกรณีอัปเดตระบบ
            Schema::table('sale_items', function (Blueprint $table) {
                if (!Schema::hasColumn('sale_items', 'discount')) {
                    // $table->decimal('discount', 15, 2)->default(0)->after('unit_price');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
}
