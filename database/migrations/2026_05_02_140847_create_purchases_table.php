<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('doc_no')->unique();
            $table->foreignId('supplier_id')->constrained('customers'); // หรือ suppliers ถ้ามี
            $table->foreignId('branch_id')->constrained('branches');
            $table->date('doc_date');
            $table->date('due_date');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('vat', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('vat_rate', 5, 2)->default(7.00);
            $table->text('note')->nullable();
            $table->string('status')->default('ค้างชำระ'); // ชำระแล้ว, ค้างชำระ, ยกเลิก
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
