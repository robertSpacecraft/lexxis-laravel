<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Street;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoAddressesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'demo@lexxis.test')->firstOrFail();

        // Streets ya existentes (DEV): usamos una de Madrid y otra de Alicante/Alacant si existe
        $streetMadrid = Street::whereHas('city', fn ($q) => $q->where('name', 'Madrid'))->first();
        $streetAlicante = Street::whereHas('city', fn ($q) => $q->where('name', 'Alicante/Alacant'))->first();

        // Fallback: si no están esas, usamos cualquier street existente para no romper el seeder
        $fallbackStreet = Street::query()->firstOrFail();

        $streetMadrid = $streetMadrid ?? $fallbackStreet;
        $streetAlicante = $streetAlicante ?? $fallbackStreet;

        // SHIPPING
        Address::updateOrCreate(
            ['user_id' => $user->id, 'alias' => 'Casa'],
            [
                'street_id' => $streetMadrid->id,
                'street_number' => '10',
                'floor' => '3',
                'door' => 'B',
                'address_type' => 'shipping',
                'contact_phone' => $user->phone,
            ]
        );

        // BILLING
        Address::updateOrCreate(
            ['user_id' => $user->id, 'alias' => 'Facturación'],
            [
                'street_id' => $streetAlicante->id,
                'street_number' => '5',
                'floor' => null,
                'door' => 'A',
                'address_type' => 'billing',
                'contact_phone' => $user->phone,
            ]
        );
    }
}
