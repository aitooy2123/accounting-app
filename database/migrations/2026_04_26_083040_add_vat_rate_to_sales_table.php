<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVatRateToSalesTable extends Migration
{
    public function up()
    {
        // Only alter if table exists and column is missing
        if (Schema::hasTable('sales') && !Schema::hasColumn('sales', 'vat_rate')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->decimal('vat_rate', 5, 2)->default(7)->after('total');
            });
        }
    }

    public function down()
    {
        // Only drop column if it exists
        if (Schema::hasTable('sales') && Schema::hasColumn('sales', 'vat_rate')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->dropColumn('vat_rate');
            });
        }
    }
}
