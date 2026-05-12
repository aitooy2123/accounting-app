<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {

            // ป้องกัน create ซ้ำ
            if (Schema::hasTable('payments')) {
                return;
            }

            Schema::create('payments', function (Blueprint $table) {

                $table->id();

                $table->date('pay_date');

                $table->string('doc_no')->unique();

                $table->foreignId('supplier_id')
                    ->constrained()
                    ->onDelete('cascade');

                $table->string('remark')->nullable();

                $table->decimal('subtotal', 15, 2);

                $table->decimal('vat', 15, 2)->default(0);

                $table->decimal('amount', 15, 2);

                $table->string('status')->default('active');

                $table->timestamps();
            });

        } catch (\Exception $e) {

            // เขียน log
            Log::error('Create payments table error: ' . $e->getMessage());

            // แสดง error
            throw new \Exception(
                'ไม่สามารถสร้างตาราง payments ได้ : ' . $e->getMessage()
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        try {

            Schema::dropIfExists('payments');

        } catch (\Exception $e) {

            Log::error('Drop payments table error: ' . $e->getMessage());

            throw new \Exception(
                'ไม่สามารถลบตาราง payments ได้ : ' . $e->getMessage()
            );
        }
    }
}
