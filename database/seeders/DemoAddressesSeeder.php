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
        $users = User::query()
            ->whereIn('email', [
                'demo@lexxis.test',
                'maria@lexxis.test',
                'carlos@lexxis.test',
                'lucia@lexxis.test',
            ])
            ->get()
            ->keyBy('email');

        if ($users->isEmpty()) {
            $this->command?->warn('No hay usuarios demo para sembrar direcciones.');
            return;
        }

        $streetMadrid = Street::whereHas('city', fn ($q) => $q->where('name', 'Madrid'))->first();
        $streetAlicante = Street::whereHas('city', fn ($q) => $q->where('name', 'Alicante/Alacant'))->first();
        $streetBarcelona = Street::whereHas('city', fn ($q) => $q->where('name', 'Barcelona'))->first();
        $streetValencia = Street::whereHas('city', fn ($q) => $q->where('name', 'Valencia'))->first();

        $fallbackStreet = Street::query()->first();

        if (!$fallbackStreet) {
            $this->command?->warn('No hay calles para sembrar direcciones demo.');
            return;
        }

        $streetMadrid = $streetMadrid ?? $fallbackStreet;
        $streetAlicante = $streetAlicante ?? $fallbackStreet;
        $streetBarcelona = $streetBarcelona ?? $fallbackStreet;
        $streetValencia = $streetValencia ?? $fallbackStreet;

        $addresses = [
            [
                'email' => 'demo@lexxis.test',
                'items' => [
                    ['alias' => 'Casa', 'street' => $streetMadrid, 'street_number' => '10', 'floor' => '3', 'door' => 'B', 'address_type' => 'shipping'],
                    ['alias' => 'Facturación', 'street' => $streetAlicante, 'street_number' => '5', 'floor' => null, 'door' => 'A', 'address_type' => 'billing'],
                ],
            ],
            [
                'email' => 'maria@lexxis.test',
                'items' => [
                    ['alias' => 'Casa María', 'street' => $streetBarcelona, 'street_number' => '22', 'floor' => '1', 'door' => 'C', 'address_type' => 'shipping'],
                    ['alias' => 'Empresa María', 'street' => $streetMadrid, 'street_number' => '18', 'floor' => '4', 'door' => null, 'address_type' => 'billing'],
                ],
            ],
            [
                'email' => 'carlos@lexxis.test',
                'items' => [
                    ['alias' => 'Casa Carlos', 'street' => $streetValencia, 'street_number' => '7', 'floor' => null, 'door' => 'D', 'address_type' => 'shipping'],
                    ['alias' => 'Facturación Carlos', 'street' => $streetValencia, 'street_number' => '7', 'floor' => null, 'door' => 'D', 'address_type' => 'billing'],
                ],
            ],
            [
                'email' => 'lucia@lexxis.test',
                'items' => [
                    ['alias' => 'Casa Lucía', 'street' => $streetAlicante, 'street_number' => '14', 'floor' => '2', 'door' => 'A', 'address_type' => 'shipping'],
                ],
            ],
        ];

        foreach ($addresses as $group) {
            $user = $users->get($group['email']);

            if (!$user) {
                continue;
            }

            foreach ($group['items'] as $payload) {
                Address::updateOrCreate(
                    ['user_id' => $user->id, 'alias' => $payload['alias']],
                    [
                        'street_id' => $payload['street']->id,
                        'street_number' => $payload['street_number'],
                        'floor' => $payload['floor'],
                        'door' => $payload['door'],
                        'address_type' => $payload['address_type'],
                        'contact_phone' => $user->phone,
                    ]
                );
            }
        }
    }
}
