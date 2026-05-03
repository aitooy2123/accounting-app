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
        // สร้าง user 100 คน (ใช้ factory + faker อัตโนมัติ)
        User::factory()->count(100)->create();

        // เรียก seeder อื่น ๆ (เรียงลำดับให้ถูก)
        $this->call([
            CompanySeeder::class,
            BranchSeeder::class,
            ChartOfAccountSeeder::class,
            CustomerSeeder::class,
            SaleSeeder::class
        ]);
    }
}
