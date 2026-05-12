<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentVouchersTable extends Migration
{
    public function up()
    {
        Schema::create('payment_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('pv_no')->unique();
            $table->date('pv_date');

            $table->foreignId('payee_id')->nullable(); // ผู้รับเงิน
            $table->text('note')->nullable();

            $table->decimal('total_amount', 12, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_vouchers');
    }
}
