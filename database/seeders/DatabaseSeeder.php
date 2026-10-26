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
        // Mật khẩu admin lấy từ .env (repo public, không được ghi cứng trong code)
        $password = env('ADMIN_PASSWORD');
        if (!$password) {
            $this->command?->error('Thiếu ADMIN_PASSWORD trong .env, bỏ qua tạo tài khoản admin.');
            return;
        }

        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@tysongoaihang.site')],
            [
                'name' => 'Administrator',
                'password' => Hash::make($password),
                'is_admin' => true,
            ]
        );
    }
}
