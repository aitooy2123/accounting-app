<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // ข้อมูลพื้นฐานสินค้า
            $table->string('product_code', 50)->unique()->comment('รหัสสินค้า');
            $table->string('name')->comment('ชื่อสินค้า');
            $table->text('description')->nullable()->comment('รายละเอียดสินค้า');
            $table->string('unit', 30)->default('หน่วย')->comment('หน่วยนับ');
            $table->decimal('unit_price', 15, 2)->default(0)->comment('ราคาขายต่อหน่วย');
            $table->decimal('cost_price', 15, 2)->nullable()->comment('ราคาต้นทุนต่อหน่วย');

            // ข้อมูลหมวดหมู่
            $table->unsignedBigInteger('category_id')->nullable()->comment('หมวดหมู่สินค้า');
            $table->string('barcode', 100)->nullable()->comment('บาร์โค้ด');

            // สถานะ
            $table->boolean('is_active')->default(true)->comment('สถานะเปิดใช้งาน');
            $table->boolean('is_service')->default(false)->comment('เป็นบริการ/ไม่ใช่สินค้า');

            // ข้อมูลเพิ่มเติม
            $table->text('notes')->nullable()->comment('หมายเหตุ');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->comment('ผู้สร้าง');

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('product_code');
            $table->index('name');
            $table->index('category_id');
        });

        DB::statement("ALTER TABLE `products` COMMENT 'ตารางข้อมูลสินค้าและบริการ'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
