<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ตรวจสอบว่ามีผู้ใช้ test@test.com อยู่แล้วหรือไม่
        $exists = DB::table('users')->where('email', 'test@test.com')->exists();

        if (!$exists) {
            DB::table('users')->insert([
                [
                    'name' => 'พุทธพงศ์ พุทธนาวงศ์',
                    'email' => 'test@test.com',
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),  // เปลี่ยนรหัสผ่านตามต้องการ
                    'remember_token' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);

            $this->command->info('✅ สร้างผู้ใช้ test@test.com เรียบร้อยแล้ว');
        } else {
            $this->command->info('⏭️ ผู้ใช้ test@test.com มีอยู่แล้ว ข้ามการสร้าง');
        }
    }
}
