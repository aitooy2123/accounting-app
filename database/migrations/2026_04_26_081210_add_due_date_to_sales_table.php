<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDueDateToSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if the 'sales' table exists
        if (Schema::hasTable('sales')) {
            // Check if the 'due_date' column does NOT exist
            if (!Schema::hasColumn('sales', 'due_date')) {
                Schema::table('sales', function (Blueprint $table) {
                    $table->date('due_date')->nullable()->after('doc_date');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Check if the 'sales' table exists and the column exists before dropping
        if (Schema::hasTable('sales') && Schema::hasColumn('sales', 'due_date')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->dropColumn('due_date');
            });
        }
    }
}
