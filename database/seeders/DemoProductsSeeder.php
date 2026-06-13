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
                'name' => 'Lexxis Future',
                'slug' => 'lexxis-future',
                'short_description' => 'Modelo conceptual con líneas técnicas.',
                'description' => 'Producto demo textual para reconstruir entornos sin depender de imágenes persistentes.',
                'type' => 'footwear',
                'base_price' => 119.90,
                'is_active' => true,
            ],
            [
                'name' => 'Lexxis Summer',
                'slug' => 'lexxis-summer',
                'short_description' => 'Modelo ligero orientado a clima cálido.',
                'description' => 'Producto demo textual para catálogo y variantes, con imágenes gestionadas manualmente desde el panel admin.',
                'type' => 'footwear',
                'base_price' => 84.90,
                'is_active' => true,
            ],
            [
                'name' => 'Lexxis Sport',
                'slug' => 'lexxis-xport',
                'short_description' => 'Modelo deportivo de respuesta flexible.',
                'description' => 'Producto demo textual. Se respeta el slug actual lexxis-xport.',
                'type' => 'footwear',
                'base_price' => 109.90,
                'is_active' => true,
            ],
            [
                'name' => 'Lexxis Mocca',
                'slug' => 'lexxis-mocca',
                'short_description' => 'Modelo urbano con acabado sobrio.',
                'description' => 'Producto demo textual para variantes y pedidos de demostración.',
                'type' => 'footwear',
                'base_price' => 94.90,
                'is_active' => true,
            ],
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
            Product::firstOrCreate(
                ['slug' => $payload['slug']],
                $payload
            );
        }
    }
}
