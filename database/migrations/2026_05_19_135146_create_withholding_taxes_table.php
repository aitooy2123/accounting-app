<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class CreateWithholdingTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // ตรวจสอบว่าตารางมีอยู่แล้วหรือไม่
        if (Schema::hasTable('withholding_taxes')) {
            echo "ตาราง withholding_taxes มีอยู่แล้ว ข้ามการสร้าง\n";
            return;
        }

        try {
            Schema::create('withholding_taxes', function (Blueprint $table) {
                $table->id();

                // foreignId ต้องมั่นใจว่าตาราง companies มีอยู่ก่อน
                if (Schema::hasTable('companies')) {
                    $table->foreignId('company_id')->constrained()->onDelete('cascade');
                } else {
                    // ถ้าไม่มีตาราง companies ให้สร้างเป็น普通 integer (ต้องไปสร้างทีหลัง)
                    $table->unsignedBigInteger('company_id');
                    echo "Warning: ตาราง companies ไม่พบ สร้าง company_id เป็น普通 integer\n";
                }

                // expense_id optional
                if (Schema::hasTable('expenses')) {
                    $table->foreignId('expense_id')->nullable()->constrained()->onDelete('set null');
                } else {
                    $table->unsignedBigInteger('expense_id')->nullable();
                    echo "Warning: ตาราง expenses ไม่พบ สร้าง expense_id เป็น nullable integer\n";
                }

                $table->string('withholding_no')->unique();
                $table->date('withholding_date');
                $table->string('invoice_no')->nullable();
                $table->decimal('tax_base', 15, 2);
                $table->decimal('tax_rate', 5, 2);
                $table->decimal('tax_amount', 15, 2);
                $table->text('remark')->nullable();
                $table->timestamps();

                // เพิ่ม index เพื่อความเร็วในการ query
                $table->index('withholding_date');
                $table->index('company_id');
            });

            echo "สร้างตาราง withholding_taxes สำเร็จ\n";

        } catch (QueryException $e) {
            // ดัก error จาก database โดยเฉพาะ
            if ($e->errorInfo[1] == 1005) { // foreign key constraint error
                echo "Error: ไม่สามารถสร้าง foreign key ได้ เนื่องจากตารางอ้างอิงไม่มีอยู่\n";
                echo "รายละเอียด: " . $e->getMessage() . "\n";
            } else {
                echo "เกิดข้อผิดพลาดในการสร้างตาราง: " . $e->getMessage() . "\n";
            }
            throw $e; // ถ้าอยากให้ migration หยุดทำงาน
        } catch (\Exception $e) {
            echo "เกิดข้อผิดพลาดทั่วไป: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // ตรวจสอบว่าตารางมีอยู่ก่อนลบ
        if (Schema::hasTable('withholding_taxes')) {
            Schema::dropIfExists('withholding_taxes');
            echo "ลบตาราง withholding_taxes เรียบร้อย\n";
        } else {
            echo "ตาราง withholding_taxes ไม่มีอยู่ ข้ามการลบ\n";
        }
    }
}
