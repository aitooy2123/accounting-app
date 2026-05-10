<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('ชื่อหมวดหมู่');
            $table->string('code', 50)->nullable()->comment('รหัสหมวดหมู่');
            $table->text('description')->nullable()->comment('คำอธิบาย');
            $table->foreignId('parent_id')->nullable()->constrained('product_categories')->nullOnDelete()->comment('หมวดหมู่หลัก');
            $table->boolean('is_active')->default(true)->comment('สถานะเปิดใช้งาน');
            $table->integer('sort_order')->default(0)->comment('ลำดับการแสดงผล');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE `product_categories` COMMENT 'ตารางหมวดหมู่สินค้า'");
    }

    public function down(): void
    {
        Schema::dropIfExists('product_categories');
    }
};
