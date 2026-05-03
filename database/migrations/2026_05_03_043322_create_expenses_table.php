<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('doc_no')->unique();
            $table->date('expense_date');
            $table->foreignId('account_id')->nullable()->constrained('chart_of_accounts');
            $table->foreignId('branch_id')->nullable()->constrained('branches');
            $table->decimal('amount', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->string('status')->default('บันทึก');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
