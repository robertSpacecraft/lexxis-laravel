<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class DemoProductsSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Lexxis Flow',
                'slug' => 'lexxis-flow',
                'short_description' => 'Modelo deportivo ligero con enfoque urbano.',
                'description' => 'Producto base orientado a un uso diario, pensado para fabricación bajo demanda y futura personalización por material, color y talla.',
                'type' => 'footwear',
                'base_price' => 89.90,
                'is_active' => true,
            ],
            [
                'name' => 'Lexxis Urban',
                'slug' => 'lexxis-urban',
                'short_description' => 'Modelo casual con mayor presencia visual.',
                'description' => 'Producto base de estética urbana, apto para variantes cerradas y para diseños personalizados generados por el usuario.',
                'type' => 'footwear',
                'base_price' => 99.90,
                'is_active' => true,
            ],
        ];

        foreach ($products as $payload) {
            Product::updateOrCreate(
                ['slug' => $payload['slug']],
                $payload
            );
        }
    }
}
