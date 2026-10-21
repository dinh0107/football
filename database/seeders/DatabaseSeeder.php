<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Tạo tài khoản Admin chính thức (Không dùng Factory/fake để tương thích 100% Production Plesk)
        User::updateOrCreate(
            ['email' => 'admin@tysongoaihang.site'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('Admin@123456'),
                'is_admin' => true,
            ]
        );

        $this->call(AdminUserSeeder::class);
    }
}
