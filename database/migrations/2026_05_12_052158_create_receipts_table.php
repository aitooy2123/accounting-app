<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('receipts')) {
            Schema::create('receipts', function (Blueprint $table) {
                $table->id();
                $table->date('receipt_date');
                $table->string('receipt_no')->nullable();
                $table->foreignId('customer_id')->nullable();
                $table->decimal('total', 12, 2)->default(0);
                $table->decimal('vat', 12, 2)->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
