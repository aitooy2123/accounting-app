<?php

namespace Database\Seeders;

use App\Models\ChartOfAccount;
use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = ChartOfAccount::where('category', 'expense')
            ->where('is_active', true)
            ->where('is_group', false)
            ->get();

        $branches = Branch::all();

        if ($accounts->isEmpty() || $branches->isEmpty()) {
            $this->command->error('❌ ต้องมี ChartOfAccount และ Branch ก่อน');
            return;
        }

        $created = 0;
        $updated = 0;
        $docPrefix = 'EXP-' . date('Ym') . '-';

        for ($i = 1; $i <= 50; $i++) {
            $account = $accounts->random();
            $branch = $branches->random();

            // สุ่มวันที่ย้อนหลัง 30 วัน
            $expenseDate = Carbon::now()->subDays(rand(0, 30));
            $amount = rand(500, 50000);

            $docNo = $docPrefix . str_pad($i, 4, '0', STR_PAD_LEFT);

            $result = DB::table('expenses')->updateOrInsert(
                ['doc_no' => $docNo],
                [
                    'expense_date' => $expenseDate->format('Y-m-d'),
                    'account_id' => $account->id,
                    'branch_id' => $branch->id,
                    'amount' => $amount,
                    'description' => 'ค่าใช้จ่าย: ' . $account->name_th,
                    'status' => 'บันทึก',
                    'created_by' => 1,
                    'created_at' => $expenseDate,
                    'updated_at' => $expenseDate,
                ]
            );

            $result ? $created++ : $updated++;
        }

        $this->command->info("✅ ExpenseSeeder: เพิ่ม {$created} รายการ, อัปเดต {$updated} รายการ");
    }
}
