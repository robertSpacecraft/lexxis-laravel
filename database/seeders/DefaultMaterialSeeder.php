<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;

class DefaultMaterialSeeder extends Seeder
{
    public function run(): void
    {
        $materials = [
            [
                'slug' => 'material-estandar',
                'name' => 'Material estándar',
                'material_type' => 'standard',
                'brand' => null,
                'supplier' => null,
                'shore_a' => null,
                'shore_scale' => null,
                'shore_value' => null,
                'description' => 'Material base por defecto.',
                'is_active' => true,
            ],
            [
                'slug' => 'tpu-flex-95a',
                'name' => 'TPU Flex 95A',
                'material_type' => 'tpu',
                'brand' => 'Generic',
                'supplier' => 'Proveedor Demo',
                'shore_a' => 95,
                'shore_scale' => 'A',
                'shore_value' => 95,
                'description' => 'Material flexible orientado a calzado impreso en 3D.',
                'is_active' => true,
            ],
            [
                'slug' => 'tpu-comfort-90a',
                'name' => 'TPU Comfort 90A',
                'material_type' => 'tpu',
                'brand' => 'Generic',
                'supplier' => 'Proveedor Demo',
                'shore_a' => 90,
                'shore_scale' => 'A',
                'shore_value' => 90,
                'description' => 'Material flexible más blando, pensado para mayor confort.',
                'is_active' => true,
            ],
            [
                'slug' => 'pla-display',
                'name' => 'PLA Display',
                'material_type' => 'pla',
                'brand' => 'Generic',
                'supplier' => 'Proveedor Demo',
                'shore_a' => null,
                'shore_scale' => null,
                'shore_value' => null,
                'description' => 'Material rígido orientado a prototipado y visualización.',
                'is_active' => true,
            ],
        ];

        foreach ($materials as $payload) {
            Material::updateOrCreate(
                ['slug' => $payload['slug']],
                $payload
            );
        }
    }
}
