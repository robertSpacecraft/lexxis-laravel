<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Street;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DevStreetsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**
         * Calles mínimas para desarrollo.
         * No pretende ser un dataset real.
         * Objetivo: permitir creación de direcciones y pedidos.
         */

// Municipios reales existentes en la BD
        $targetCities = [
            'Madrid',
            'Barcelona',
            'Valencia',
            'Sevilla',
            'Alicante/Alacant',
        ];

        $cities = City::whereIn('name', $targetCities)
            ->get()
            ->keyBy('name');

        // Calles mínimas por ciudad (DEV)
        $streetsByCity = [

            'Madrid' => [
                ['name' => 'Gran Vía', 'street_type' => 'avenue'],
            ],

            'Barcelona' => [
                ['name' => 'Avinguda Diagonal', 'street_type' => 'avenue'],
            ],

            'Valencia' => [
                ['name' => 'Carrer de Colón', 'street_type' => 'street'],
            ],

            'Sevilla' => [
                ['name' => 'Avenida de la Constitución', 'street_type' => 'avenue'],
            ],

            'Alicante/Alacant' => [
                ['name' => 'Avenida Alfonso X el Sabio', 'street_type' => 'avenue'],
            ],

            // Aquí podrás añadir más ciudades/calles si lo necesitas
        ];

        foreach ($streetsByCity as $cityName => $streets) {

            if (! $cities->has($cityName)) {
                $this->command?->warn("Ciudad no encontrada: {$cityName}");
                continue;
            }

            $city = $cities->get($cityName);

            foreach ($streets as $streetData) {
                Street::updateOrCreate(
                    [
                        'name' => $streetData['name'],
                        'city_id' => $city->id,
                    ],
                    [
                        'street_type' => $streetData['street_type'] ?? null,
                    ]
                );
            }
        }
    }
}
