<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed initial Administrator account for Phase 8.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@premierleague.local'],
            [
                'name' => 'Hệ Thống Quản Trị Viên',
                'password' => Hash::make('admin123456'),
                'is_admin' => true,
            ]
        );
    }
}
