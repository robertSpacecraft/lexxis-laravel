<?php

namespace Database\Seeders;

use App\Enums\ProductDesignStatus;
use App\Models\Material;
use App\Models\Product;
use App\Models\ProductDesign;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoProductDesignsSeeder extends Seeder
{
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

        $products = Product::query()
            ->whereIn('slug', [
                'lexxis-flow',
                'lexxis-urban',
                'lexxis-future',
                'lexxis-summer',
                'lexxis-xport',
                'lexxis-mocca',
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

        if ($users->isEmpty() || $products->isEmpty() || $materials->isEmpty()) {
            $this->command?->warn('Faltan usuarios, productos o materiales para sembrar diseños demo.');
            return;
        }

        $designs = [
            [
                'email' => 'demo@lexxis.test',
                'product_slug' => 'lexxis-flow',
                'material_slug' => 'tpu-flex-95a',
                'color_name' => 'Negro',
                'size_eu' => 42.0,
                'unit_price' => 89.90,
                'pricing_breakdown' => [
                    'source' => 'demo_seed',
                    'product_base_price' => 89.90,
                    'material_adjustment' => 0.00,
                    'size_adjustment' => 0.00,
                ],
                'status' => ProductDesignStatus::Draft,
            ],
            [
                'email' => 'demo@lexxis.test',
                'product_slug' => 'lexxis-urban',
                'material_slug' => 'tpu-comfort-90a',
                'color_name' => 'Arena',
                'size_eu' => 43.0,
                'unit_price' => 104.90,
                'pricing_breakdown' => [
                    'source' => 'demo_seed',
                    'product_base_price' => 99.90,
                    'material_adjustment' => 5.00,
                    'size_adjustment' => 0.00,
                ],
                'status' => ProductDesignStatus::InCart,
            ],
            [
                'email' => 'maria@lexxis.test',
                'product_slug' => 'lexxis-flow',
                'material_slug' => 'tpu-comfort-90a',
                'color_name' => 'Azul',
                'size_eu' => 40.0,
                'unit_price' => 94.90,
                'pricing_breakdown' => [
                    'source' => 'demo_seed',
                    'product_base_price' => 89.90,
                    'material_adjustment' => 5.00,
                    'size_adjustment' => 0.00,
                ],
                'status' => ProductDesignStatus::Ordered,
            ],
            [
                'email' => 'carlos@lexxis.test',
                'product_slug' => 'lexxis-xport',
                'material_slug' => 'tpu-flex-95a',
                'color_name' => 'Rojo',
                'size_eu' => 44.0,
                'unit_price' => 114.90,
                'pricing_breakdown' => [
                    'source' => 'demo_seed',
                    'product_base_price' => 109.90,
                    'material_adjustment' => 5.00,
                    'size_adjustment' => 0.00,
                ],
                'status' => ProductDesignStatus::Draft,
            ],
            [
                'email' => 'lucia@lexxis.test',
                'product_slug' => 'lexxis-summer',
                'material_slug' => 'tpu-comfort-90a',
                'color_name' => 'Blanco',
                'size_eu' => 38.0,
                'unit_price' => 89.90,
                'pricing_breakdown' => [
                    'source' => 'demo_seed',
                    'product_base_price' => 84.90,
                    'material_adjustment' => 5.00,
                    'size_adjustment' => 0.00,
                ],
                'status' => ProductDesignStatus::Archived,
            ],
        ];

        foreach ($designs as $payload) {
            $user = $users->get($payload['email']);
            $product = $products->get($payload['product_slug']);
            $material = $materials->get($payload['material_slug']);

            if (!$user || !$product || !$material) {
                $this->command?->warn('Se omite diseño demo por faltar usuario, producto o material.');
                continue;
            }

            ProductDesign::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'material_id' => $material->id,
                    'color_name' => $payload['color_name'],
                    'size_eu' => $payload['size_eu'],
                ],
                [
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'material_id' => $material->id,
                    'color_name' => $payload['color_name'],
                    'size_eu' => $payload['size_eu'],
                    'unit_price' => $payload['unit_price'],
                    'pricing_breakdown' => $payload['pricing_breakdown'],
                    'status' => $payload['status'],
                ]
            );
        }
    }
}
