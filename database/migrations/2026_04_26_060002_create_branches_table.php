<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchesTable extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('branches')) {
            Schema::create('branches', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete()->comment('รหัสบริษัท');
                $table->string('code');
                $table->string('name');
                $table->string('address')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('manager')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        } else {
            Schema::table('branches', function (Blueprint $table) {
                // เพิ่ม company_id ถ้ายังไม่มี
                if (!Schema::hasColumn('branches', 'company_id')) {
                    $table->foreignId('company_id')->after('id')->constrained('companies')->cascadeOnDelete()->comment('รหัสบริษัท');
                }
                if (!Schema::hasColumn('branches', 'code')) {
                    $table->string('code')->after('company_id');
                }
                if (!Schema::hasColumn('branches', 'name')) {
                    $table->string('name')->after('code');
                }
                if (!Schema::hasColumn('branches', 'address')) {
                    $table->string('address')->nullable()->after('name');
                }
                if (!Schema::hasColumn('branches', 'phone')) {
                    $table->string('phone')->nullable()->after('address');
                }
                if (!Schema::hasColumn('branches', 'email')) {
                    $table->string('email')->nullable()->after('phone');
                }
                if (!Schema::hasColumn('branches', 'manager')) {
                    $table->string('manager')->nullable()->after('email');
                }
                if (!Schema::hasColumn('branches', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('manager');
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('branches')) {
            Schema::table('branches', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
            });
            Schema::dropIfExists('branches');
        }
    }
}
