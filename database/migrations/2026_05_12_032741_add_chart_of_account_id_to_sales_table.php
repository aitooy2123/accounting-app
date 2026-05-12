<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChartOfAccountIdToSalesTable extends Migration
{
    public function up()
    {
        // ดักก่อนว่ามี column หรือยัง
        if (!Schema::hasColumn('sales', 'chart_of_account_id')) {

            Schema::table('sales', function (Blueprint $table) {

                $table->unsignedBigInteger('chart_of_account_id')
                    ->nullable()
                    ->after('status');

            });

        }
    }

    public function down()
    {
        // ดักก่อนว่ามี column จริงไหม
        if (Schema::hasColumn('sales', 'chart_of_account_id')) {

            Schema::table('sales', function (Blueprint $table) {

                $table->dropColumn('chart_of_account_id');

            });

        }
    }
}
