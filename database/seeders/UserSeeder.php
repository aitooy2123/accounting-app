<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [

            [
                'name' => 'พุทธพงศ์ พุทธนาวงศ์',
                'email' => 'test@test.com',
                'password' => 'password',
            ],

            [
                'name' => 'Administrator',
                'email' => 'admin@test.com',
                'password' => '12345678',
            ],

        ];

        foreach ($users as $user) {

            $exists = DB::table('users')
                ->where('email', $user['email'])
                ->exists();

            if (!$exists) {

                DB::table('users')->insert([
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'email_verified_at' => now(),
                    'password' => Hash::make($user['password']),
                    'remember_token' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->command->info("✅ สร้างผู้ใช้ {$user['email']} เรียบร้อยแล้ว");

            } else {

                $this->command->info("⏭️ {$user['email']} มีอยู่แล้ว");

            }
        }
    }
}
