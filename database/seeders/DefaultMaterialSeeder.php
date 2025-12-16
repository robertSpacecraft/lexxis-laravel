<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Material;

class DefaultMaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Crea materiales por defecto
        Material::firstOrCreate(
            ['slug' => 'material-estandar'],
            [
                'name' => 'Material estándar',
                'material_type' => 'standard',
                'is_active' => true,
            ]
        );


    }
}
