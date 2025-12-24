<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            ['name' => 'Afghanistan', 'iso_code' => 'AFG'],
            ['name' => 'Albania', 'iso_code' => 'ALB'],
            ['name' => 'Algeria', 'iso_code' => 'DZA'],
            ['name' => 'Andorra', 'iso_code' => 'AND'],
            ['name' => 'Argentina', 'iso_code' => 'ARG'],
            ['name' => 'Australia', 'iso_code' => 'AUS'],
            ['name' => 'Austria', 'iso_code' => 'AUT'],
            ['name' => 'Belgium', 'iso_code' => 'BEL'],
            ['name' => 'Brazil', 'iso_code' => 'BRA'],
            ['name' => 'Canada', 'iso_code' => 'CAN'],
            ['name' => 'China', 'iso_code' => 'CHN'],
            ['name' => 'Denmark', 'iso_code' => 'DNK'],
            ['name' => 'Finland', 'iso_code' => 'FIN'],
            ['name' => 'France', 'iso_code' => 'FRA'],
            ['name' => 'Germany', 'iso_code' => 'DEU'],
            ['name' => 'Greece', 'iso_code' => 'GRC'],
            ['name' => 'Ireland', 'iso_code' => 'IRL'],
            ['name' => 'Italy', 'iso_code' => 'ITA'],
            ['name' => 'Japan', 'iso_code' => 'JPN'],
            ['name' => 'Mexico', 'iso_code' => 'MEX'],
            ['name' => 'Netherlands', 'iso_code' => 'NLD'],
            ['name' => 'Norway', 'iso_code' => 'NOR'],
            ['name' => 'Poland', 'iso_code' => 'POL'],
            ['name' => 'Portugal', 'iso_code' => 'PRT'],
            ['name' => 'Spain', 'iso_code' => 'ESP'],
            ['name' => 'Sweden', 'iso_code' => 'SWE'],
            ['name' => 'Switzerland', 'iso_code' => 'CHE'],
            ['name' => 'United Kingdom', 'iso_code' => 'GBR'],
            ['name' => 'United States', 'iso_code' => 'USA'],
        ];
        foreach ($countries as $country) {
            Country::updateOrCreate(
                ['iso_code' => $country['iso_code']],
                ['name' => $country['name']]
            );
        }
    }
}
