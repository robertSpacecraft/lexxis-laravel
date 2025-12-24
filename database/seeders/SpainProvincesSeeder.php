<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Province;
use Illuminate\Database\Seeder;

class SpainProvincesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $spain = Country::where('iso_code', 'ESP')->firstOrFail();

        $provinces = [
            ['name' => 'A Coruña'],
            ['name' => 'Álava'],
            ['name' => 'Albacete'],
            ['name' => 'Alicante'],
            ['name' => 'Almería'],
            ['name' => 'Asturias'],
            ['name' => 'Ávila'],
            ['name' => 'Badajoz'],
            ['name' => 'Barcelona'],
            ['name' => 'Burgos'],
            ['name' => 'Cáceres'],
            ['name' => 'Cádiz'],
            ['name' => 'Cantabria'],
            ['name' => 'Castellón'],
            ['name' => 'Ciudad Real'],
            ['name' => 'Córdoba'],
            ['name' => 'Cuenca'],
            ['name' => 'Girona'],
            ['name' => 'Granada'],
            ['name' => 'Guadalajara'],
            ['name' => 'Guipúzcoa'],
            ['name' => 'Huelva'],
            ['name' => 'Huesca'],
            ['name' => 'Illes Balears'],
            ['name' => 'Jaén'],
            ['name' => 'La Rioja'],
            ['name' => 'Las Palmas'],
            ['name' => 'León'],
            ['name' => 'Lleida'],
            ['name' => 'Lugo'],
            ['name' => 'Madrid'],
            ['name' => 'Málaga'],
            ['name' => 'Murcia'],
            ['name' => 'Navarra'],
            ['name' => 'Ourense'],
            ['name' => 'Palencia'],
            ['name' => 'Pontevedra'],
            ['name' => 'Salamanca'],
            ['name' => 'Santa Cruz de Tenerife'],
            ['name' => 'Segovia'],
            ['name' => 'Sevilla'],
            ['name' => 'Soria'],
            ['name' => 'Tarragona'],
            ['name' => 'Teruel'],
            ['name' => 'Toledo'],
            ['name' => 'Valencia'],
            ['name' => 'Valladolid'],
            ['name' => 'Vizcaya'],
            ['name' => 'Zamora'],
            ['name' => 'Zaragoza'],
        ];

        foreach ($provinces as $province) {
            Province::updateOrCreate(
                [
                    'country_id' => $spain->id,
                    'name'       => $province['name'],
                ],
                []
            );
        }
    }
}
