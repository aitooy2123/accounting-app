<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class CreateCustomersTable extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('name');
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->text('address')->nullable();
                $table->string('tax_id')->nullable();
                $table->string('contact_person')->nullable();
                $table->string('contact_phone')->nullable();

                // ✅ เพิ่ม 2 คอลัมน์นี้
                $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete()->comment('รหัสสาขา');
                $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete()->comment('รหัสบริษัท');

                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        } else {
            Schema::table('customers', function (Blueprint $table) {
                if (!Schema::hasColumn('customers', 'code')) {
                    $table->string('code')->unique()->after('id');
                }
                if (!Schema::hasColumn('customers', 'name')) {
                    $table->string('name')->after('code');
                }
                if (!Schema::hasColumn('customers', 'email')) {
                    $table->string('email')->nullable()->after('name');
                }
                if (!Schema::hasColumn('customers', 'phone')) {
                    $table->string('phone')->nullable()->after('email');
                }
                if (!Schema::hasColumn('customers', 'address')) {
                    $table->text('address')->nullable()->after('phone');
                }
                if (!Schema::hasColumn('customers', 'tax_id')) {
                    $table->string('tax_id')->nullable()->after('address');
                }
                if (!Schema::hasColumn('customers', 'contact_person')) {
                    $table->string('contact_person')->nullable()->after('tax_id');
                }
                if (!Schema::hasColumn('customers', 'contact_phone')) {
                    $table->string('contact_phone')->nullable()->after('contact_person');
                }

                // ✅ เพิ่มการตรวจสอบ 2 คอลัมน์นี้
                if (!Schema::hasColumn('customers', 'branch_id')) {
                    $table->foreignId('branch_id')->nullable()->after('contact_phone')->constrained('branches')->nullOnDelete()->comment('รหัสสาขา');
                }
                if (!Schema::hasColumn('customers', 'company_id')) {
                    $table->foreignId('company_id')->nullable()->after('branch_id')->constrained('companies')->nullOnDelete()->comment('รหัสบริษัท');
                }

                if (!Schema::hasColumn('customers', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('company_id');
                }
            });

             // ✅ เรียก Seeder หลังจากสร้างตาราง
            Artisan::call('db:seed', [
                '--class' => 'CustomerSeeder',
                '--force' => true,
            ]);
        }
    }

    public function down()
    {
        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropForeign(['company_id']);
            });
            Schema::dropIfExists('customers');
        }
    }
}
