<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'demo@lexxis.test'],
            [
                'name' => 'Demo',
                'last_name' => 'Customer',
                'phone' => '600000000',
                'role' => UserRole::CUSTOMER,
                'is_active' => true,
                'password' => Hash::make('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'maria@lexxis.test'],
            [
                'name' => 'María',
                'last_name' => 'Demo',
                'phone' => '611111111',
                'role' => UserRole::CUSTOMER,
                'is_active' => true,
                'password' => Hash::make('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@lexxis.test'],
            [
                'name' => 'Admin',
                'last_name' => 'Lexxis',
                'phone' => '699999999',
                'role' => UserRole::ADMIN,
                'is_active' => true,
                'password' => Hash::make('password'),
            ]
        );
    }
}
