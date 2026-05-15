<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {

            $table->id();

            $table->string('doc_no')->unique();

            $table->date('expense_date');

            $table->unsignedBigInteger('payee_id')->nullable();

            $table->unsignedBigInteger('account_id')->nullable();

            $table->unsignedBigInteger('branch_id')->nullable();

            $table->decimal('amount', 15, 2)->default(0);

            $table->text('description')->nullable();

            $table->text('remark')->nullable();

            $table->string('status')->default('บันทึก');

            $table->unsignedBigInteger('created_by')->nullable();

            $table->softDeletes();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('expenses');
    }
};
