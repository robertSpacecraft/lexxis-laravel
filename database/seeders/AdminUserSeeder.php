<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@lexxis.local'],
            [
                'name' => 'Admin',
                'last_name' => 'Lexxis',
                'phone' => '600000001',
                'role' => UserRole::ADMIN,
                'is_active' => true,
                'password' => Hash::make('Admin12345!'),
            ]
        );
    }
}

