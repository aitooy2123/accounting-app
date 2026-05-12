<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToPaymentsTable extends Migration
{
    public function up()
    {
        // ดักกรณี table ไม่มี
        if (!Schema::hasTable('payments')) {
            return;
        }

        // ดักกรณี column มีอยู่แล้ว
        if (!Schema::hasColumn('payments', 'type')) {

            Schema::table('payments', function (Blueprint $table) {

                $table->enum('type', ['payment', 'receipt'])
                    ->default('payment')
                    ->after('pay_date');

            });
        }
    }

    public function down()
    {
        // ดักกรณี table ไม่มี
        if (!Schema::hasTable('payments')) {
            return;
        }

        // ดักกรณี column ไม่มี
        if (Schema::hasColumn('payments', 'type')) {

            Schema::table('payments', function (Blueprint $table) {

                $table->dropColumn('type');

            });
        }
    }
}
