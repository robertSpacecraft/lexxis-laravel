<?php

namespace Database\Seeders;

use App\Models\Material;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class DemoProductVariantsSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::query()
            ->whereIn('slug', [
                'lexxis-future',
                'lexxis-summer',
                'lexxis-xport',
                'lexxis-mocca',
                'lexxis-flow',
                'lexxis-urban',
            ])
            ->get()
            ->keyBy('slug');

        $materials = Material::query()
            ->whereIn('slug', [
                'tpu-flex-95a',
                'tpu-comfort-90a',
                'pla-display',
            ])
            ->get()
            ->keyBy('slug');

        if ($products->isEmpty() || $materials->isEmpty()) {
            $this->command?->warn('Faltan productos o materiales para sembrar variantes demo.');
            return;
        }

        $variants = [
            [
                'product_slug' => 'lexxis-future',
                'material_slug' => 'tpu-flex-95a',
                'sku' => 'FUTURE-BLK-42-95A',
                'size_eu' => 42.0,
                'color_name' => 'Negro',
                'price' => 124.90,
                'stock' => 4,
                'is_active' => true,
            ],
            [
                'product_slug' => 'lexxis-summer',
                'material_slug' => 'tpu-comfort-90a',
                'sku' => 'SUMMER-SND-39-90A',
                'size_eu' => 39.0,
                'color_name' => 'Arena',
                'price' => 89.90,
                'stock' => 6,
                'is_active' => true,
            ],
            [
                'product_slug' => 'lexxis-xport',
                'material_slug' => 'tpu-flex-95a',
                'sku' => 'XPORT-BLU-43-95A',
                'size_eu' => 43.0,
                'color_name' => 'Azul',
                'price' => 114.90,
                'stock' => 5,
                'is_active' => true,
            ],
            [
                'product_slug' => 'lexxis-mocca',
                'material_slug' => 'pla-display',
                'sku' => 'MOCCA-BRN-41-PLA',
                'size_eu' => 41.0,
                'color_name' => 'Mocca',
                'price' => 99.90,
                'stock' => 3,
                'is_active' => true,
            ],
            [
                'product_slug' => 'lexxis-flow',
                'material_slug' => 'tpu-flex-95a',
                'sku' => 'FLOW-BLK-42-95A',
                'size_eu' => 42.0,
                'color_name' => 'Negro',
                'price' => 92.90,
                'stock' => 5,
                'is_active' => true,
            ],
            [
                'product_slug' => 'lexxis-flow',
                'material_slug' => 'tpu-comfort-90a',
                'sku' => 'FLOW-SND-41-90A',
                'size_eu' => 41.0,
                'color_name' => 'Arena',
                'price' => 94.90,
                'stock' => 2,
                'is_active' => true,
            ],
            [
                'product_slug' => 'lexxis-urban',
                'material_slug' => 'tpu-flex-95a',
                'sku' => 'URBAN-WHT-43-95A',
                'size_eu' => 43.0,
                'color_name' => 'Blanco',
                'price' => 104.90,
                'stock' => 3,
                'is_active' => true,
            ],
            [
                'product_slug' => 'lexxis-urban',
                'material_slug' => 'pla-display',
                'sku' => 'URBAN-RED-42-PLA',
                'size_eu' => 42.0,
                'color_name' => 'Rojo',
                'price' => 109.90,
                'stock' => 1,
                'is_active' => true,
            ],
        ];

        foreach ($variants as $payload) {
            $product = $products->get($payload['product_slug']);
            $material = $materials->get($payload['material_slug']);

            if (!$product || !$material) {
                $this->command?->warn("Se omite variante demo {$payload['sku']} por faltar producto o material.");
                continue;
            }

            unset($payload['product_slug'], $payload['material_slug']);

            ProductVariant::updateOrCreate(
                ['sku' => $payload['sku']],
                [
                    ...$payload,
                    'product_id' => $product->id,
                    'material_id' => $material->id,
                ]
            );
        }
    }
}
