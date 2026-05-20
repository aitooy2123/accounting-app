<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWithholdingTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

       public function up()
{
    Schema::create('withholding_taxes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('expense_id')->constrained()->onDelete('cascade');
        $table->string('withholding_number')->unique();
        $table->date('date');
        $table->string('invoice_number');
        $table->decimal('amount_before_withholding', 15, 2);
        $table->decimal('withholding_rate', 5, 2);
        $table->decimal('withholding_amount', 15, 2);
        $table->text('remark')->nullable();
        $table->timestamps();
    });
}
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('withholding_taxes');
    }
}
