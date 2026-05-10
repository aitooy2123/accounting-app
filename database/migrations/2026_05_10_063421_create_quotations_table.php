<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();

            // ข้อมูลเอกสาร
            $table->string('quotation_number', 50)->unique()->comment('เลขที่ใบเสนอราคา (QT-ปี-รันนิง)');
            $table->string('reference_number', 100)->nullable()->comment('เลขที่อ้างอิง (ถ้ามี)');
            $table->date('quotation_date')->comment('วันที่ออกใบเสนอราคา');
            $table->date('expiry_date')->nullable()->comment('วันหมดอายุใบเสนอราคา');
            $table->string('status', 30)->default('draft')->comment('สถานะ: draft, sent, approved, rejected, expired, converted, cancelled');

            // ข้อมูลผู้ขาย (Seller)
            $table->string('seller_company_name')->comment('ชื่อบริษัทผู้ขาย');
            $table->string('seller_tax_id', 30)->nullable()->comment('เลขประจำตัวผู้เสียภาษีผู้ขาย');
            $table->text('seller_address')->nullable()->comment('ที่อยู่ผู้ขาย');
            $table->string('seller_phone', 30)->nullable()->comment('เบอร์โทรผู้ขาย');
            $table->string('seller_email', 100)->nullable()->comment('อีเมลผู้ขาย');
            $table->string('seller_contact_person', 100)->nullable()->comment('ผู้ติดต่อฝั่งผู้ขาย');
            $table->string('seller_branch', 100)->nullable()->comment('สาขาผู้ขาย');

            // ข้อมูลผู้ซื้อ (Buyer)
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete()->comment('รหัสลูกค้า (ถ้ามีในระบบ)');
            $table->string('buyer_company_name')->comment('ชื่อบริษัทผู้ซื้อ');
            $table->string('buyer_tax_id', 30)->nullable()->comment('เลขประจำตัวผู้เสียภาษีผู้ซื้อ');
            $table->text('buyer_address')->nullable()->comment('ที่อยู่ผู้ซื้อ');
            $table->string('buyer_phone', 30)->nullable()->comment('เบอร์โทรผู้ซื้อ');
            $table->string('buyer_email', 100)->nullable()->comment('อีเมลผู้ซื้อ');
            $table->string('buyer_contact_person', 100)->nullable()->comment('ผู้ติดต่อฝั่งผู้ซื้อ');
            $table->string('buyer_project_name', 200)->nullable()->comment('ชื่อโครงการ');

            // ข้อมูลทางการเงิน
            $table->decimal('subtotal', 15, 2)->default(0)->comment('ยอดรวมก่อนส่วนลด');
            $table->enum('discount_type', ['fixed', 'percentage'])->nullable()->comment('ประเภทส่วนลด (ยอดเงิน/เปอร์เซ็นต์)');
            $table->decimal('discount_value', 15, 2)->default(0)->comment('มูลค่าส่วนลด');
            $table->decimal('discount_amount', 15, 2)->default(0)->comment('จำนวนเงินส่วนลดที่คำนวณได้');
            $table->decimal('vat_rate', 5, 2)->default(7)->comment('อัตราภาษีมูลค่าเพิ่ม (%)');
            $table->decimal('vat_amount', 15, 2)->default(0)->comment('จำนวนเงินภาษีมูลค่าเพิ่ม');
            $table->decimal('withholding_tax_rate', 5, 2)->default(0)->comment('อัตราภาษีหัก ณ ที่จ่าย (%)');
            $table->decimal('withholding_tax_amount', 15, 2)->default(0)->comment('จำนวนเงินภาษีหัก ณ ที่จ่าย');
            $table->decimal('grand_total', 15, 2)->default(0)->comment('ยอดรวมสุทธิ');

            // เงื่อนไข
            $table->string('credit_terms', 200)->nullable()->comment('เงื่อนไขการชำระเงิน');
            $table->integer('credit_days')->default(0)->comment('จำนวนวันเครดิต');
            $table->text('delivery_terms')->nullable()->comment('เงื่อนไขการส่งมอบ');
            $table->date('delivery_date')->nullable()->comment('กำหนดวันส่งมอบ');
            $table->integer('warranty_period')->nullable()->comment('ระยะเวลารับประกัน (วัน)');

            // เพิ่มเติม
            $table->text('notes')->nullable()->comment('หมายเหตุ');
            $table->text('terms_conditions')->nullable()->comment('ข้อกำหนดและเงื่อนไข');

            // ข้อมูล Branch และผู้สร้าง/อนุมัติ
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete()->comment('สาขาที่สร้างเอกสาร');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->comment('ผู้สร้าง');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->comment('ผู้อนุมัติ');
            $table->timestamp('approved_at')->nullable()->comment('วันที่อนุมัติ');

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('quotation_number');
            $table->index('status');
            $table->index('quotation_date');
            $table->index('expiry_date');
            $table->index('customer_id');
            $table->index('created_by');
            $table->index(['quotation_number', 'status']);
        });

        // เพิ่ม Comment ให้ตาราง
        DB::statement("ALTER TABLE `quotations` COMMENT 'ตารางเก็บข้อมูลใบเสนอราคา'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
