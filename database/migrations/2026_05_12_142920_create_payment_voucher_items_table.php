<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentVoucherItemsTable extends Migration
{
    public function up()
    {
        Schema::create('payment_voucher_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('payment_voucher_id')->constrained()->onDelete('cascade');
            $table->foreignId('chart_of_account_id');

            $table->enum('type', ['dr', 'cr']); // เดบิต / เครดิต

            $table->decimal('amount', 12, 2)->default(0);

            $table->string('description')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_voucher_items');
    }
}
