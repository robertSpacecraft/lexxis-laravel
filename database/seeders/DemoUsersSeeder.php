<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Demo CUSTOMER
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

        // Demo ADMIN (útil para entrar al panel si lo necesitas)
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
