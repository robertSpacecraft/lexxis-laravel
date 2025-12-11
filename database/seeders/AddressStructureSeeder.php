<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\City;
use App\Models\Country;
use App\Models\Province;
use App\Models\Street;
use App\Models\User;
use Illuminate\Database\Seeder;

class AddressStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. País
        $spain = Country::create([
            'name' => 'Spain',
            'iso_code' => 'ESP',
        ]);

        // 2. Provincias
        $alicante = Province::create([
            'name' => 'Alicante',
            'country_id' => $spain->id,
        ]);

        $valencia = Province::create([
            'name' => 'Valencia',
            'country_id' => $spain->id,
        ]);

        // 3. Ciudades
        $elda = City::create([
            'name' => 'Elda',
            'postal_code' => '03600',
            'province_id' => $alicante->id,
        ]);

        $petrer = City::create([
            'name' => 'Petrer',
            'postal_code' => '03610',
            'province_id' => $alicante->id,
        ]);

        $valenciaCity = City::create([
            'name' => 'Valencia',
            'postal_code' => '46001',
            'province_id' => $valencia->id,
        ]);

        // 4. Calles
        $streetElda = Street::create([
            'name' => 'Avenida de Madrid',
            'street_type' => 'avenue',
            'city_id' => $elda->id,
        ]);

        $streetPetrer = Street::create([
            'name' => 'Calle La Mancha',
            'street_type' => 'street',
            'city_id' => $petrer->id,
        ]);

        $streetValencia = Street::create([
            'name' => 'Plaza del Ayuntamiento',
            'street_type' => 'square',
            'city_id' => $valenciaCity->id,
        ]);

        // 5. Usuario de prueba
        $user = User::factory()->create([
            'email' => 'demo@lexxis.test',
        ]);

        // 6. Direcciones del usuario
        Address::create([
            'user_id' => $user->id,
            'street_id' => $streetElda->id,
            'alias' => 'Casa Elda',
            'street_number' => '10',
            'floor' => '3',
            'door' => 'B',
            'address_type' => 'shipping',
            'contact_phone' => $user->phone,
        ]);

        Address::create([
            'user_id' => $user->id,
            'street_id' => $streetPetrer->id,
            'alias' => 'Casa Petrer',
            'street_number' => '5',
            'floor' => null,
            'door' => 'A',
            'address_type' => 'billing',
            'contact_phone' => $user->phone,
        ]);

        Address::create([
            'user_id' => $user->id,
            'street_id' => $streetValencia->id,
            'alias' => 'Oficina Valencia',
            'street_number' => '1',
            'floor' => '1',
            'door' => null,
            'address_type' => 'shipping',
            'contact_phone' => $user->phone,
        ]);
    }
}
