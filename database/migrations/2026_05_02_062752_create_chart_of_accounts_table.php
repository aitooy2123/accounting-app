<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChartOfAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chart_of_accounts', function (Blueprint $table) {
        $table->id();
        $table->string('code')->unique(); // รหัสบัญชี เช่น 1111-00
        $table->string('name_th');
        $table->string('name_en')->nullable();
        $table->enum('category', ['asset', 'liability', 'equity', 'revenue', 'expense']);
        // เชื่อมโยงกับตัวเองเพื่อทำบัญชีย่อย
        $table->foreignId('parent_id')->nullable()->constrained('chart_of_accounts')->onDelete('cascade');
        $table->boolean('is_group')->default(false); // true คือบัญชีคุม (บันทึกรายการไม่ได้), false คือบัญชีย่อย
        $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('chart_of_accounts');
    }
}
