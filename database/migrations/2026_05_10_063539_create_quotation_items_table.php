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
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();

            // Foreign Key ไปยัง quotations
            $table->foreignId('quotation_id')->constrained('quotations')->cascadeOnDelete()->comment('รหัสใบเสนอราคา');

            // ข้อมูลรายการ
            $table->integer('line_number')->default(1)->comment('ลำดับรายการ');

            // ⚠️ เปลี่ยนจาก foreignId เป็น nullable unsignedBigInteger
            $table->unsignedBigInteger('product_id')->nullable()->comment('รหัสสินค้า (ถ้ามี)');
            // ถ้าต้องการ Foreign Key จริงๆ ให้ตรวจสอบก่อนว่า products มีอยู่
            // $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();

            $table->string('product_code', 50)->nullable()->comment('รหัสสินค้า');
            $table->string('description')->comment('ชื่อสินค้า/บริการ');
            $table->text('specification')->nullable()->comment('รายละเอียด/สเปค');

            // จำนวนและราคา
            $table->decimal('quantity', 15, 4)->default(1)->comment('จำนวน');
            $table->string('unit', 30)->default('หน่วย')->comment('หน่วยนับ (ชิ้น, กล่อง, ชั่วโมง, ฯลฯ)');
            $table->decimal('unit_price', 15, 2)->default(0)->comment('ราคาต่อหน่วย');

            // ส่วนลด
            $table->enum('discount_type', ['fixed', 'percentage'])->nullable()->comment('ประเภทส่วนลด');
            $table->decimal('discount_value', 15, 2)->default(0)->comment('มูลค่าส่วนลด');
            $table->decimal('discount_amount', 15, 2)->default(0)->comment('จำนวนเงินส่วนลด');

            // การคำนวณ
            $table->decimal('amount', 15, 2)->default(0)->comment('ยอดเงิน (จำนวน x ราคาต่อหน่วย)');
            $table->decimal('net_amount', 15, 2)->default(0)->comment('ยอดเงินหลังหักส่วนลด');
            $table->decimal('vat_rate', 5, 2)->default(7)->comment('อัตราภาษี (%)');
            $table->decimal('vat_amount', 15, 2)->default(0)->comment('จำนวนภาษี');
            $table->decimal('total_amount', 15, 2)->default(0)->comment('ยอดรวมสุทธิ');

            // อื่นๆ
            $table->integer('sort_order')->default(0)->comment('ลำดับการแสดงผล');
            $table->text('notes')->nullable()->comment('หมายเหตุรายการ');

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('quotation_id');
            $table->index('product_id'); // ยังเก็บ index ไว้เพื่อประสิทธิภาพ
        });

        DB::statement("ALTER TABLE `quotation_items` COMMENT 'ตารางเก็บข้อมูลรายการสินค้า/บริการในใบเสนอราคา'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
    }
};
