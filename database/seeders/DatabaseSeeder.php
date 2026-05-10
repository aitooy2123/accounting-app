<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // เรียก seeder อื่น ๆ (เรียงลำดับให้ถูก)
        $this->call([
            CompanySeeder::class,
            BranchSeeder::class,
            ChartOfAccountSeeder::class,
            CustomerSeeder::class,
            SaleSeeder::class,
            PurchaseSeeder::class,
            InvoiceSeeder::class,   // ✅ เพิ่ม
            ExpenseSeeder::class,   // ✅ เพิ่ม
            UserSeeder::class,
        ]);

        // สร้าง user 100 คน (ใช้ factory + faker อัตโนมัติ)
        User::factory()->count(10)->create();
    }
}
