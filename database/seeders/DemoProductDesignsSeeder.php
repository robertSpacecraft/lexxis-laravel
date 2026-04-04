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
        $demoUser = User::query()->where('email', 'demo@lexxis.test')->first();
        $secondUser = User::query()->where('email', 'maria@lexxis.test')->first();

        $flow = Product::query()->where('slug', 'lexxis-flow')->first();
        $urban = Product::query()->where('slug', 'lexxis-urban')->first();

        $tpu95 = Material::query()->where('slug', 'tpu-flex-95a')->first();
        $tpu90 = Material::query()->where('slug', 'tpu-comfort-90a')->first();

        if (!$demoUser || !$flow || !$urban || !$tpu95 || !$tpu90) {
            $this->command?->warn('Faltan usuarios, productos o materiales para sembrar diseños demo.');
            return;
        }

        $designs = [
            [
                'user_id' => $demoUser->id,
                'product_id' => $flow->id,
                'material_id' => $tpu95->id,
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
                'user_id' => $demoUser->id,
                'product_id' => $urban->id,
                'material_id' => $tpu90->id,
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
        ];

        if ($secondUser) {
            $designs[] = [
                'user_id' => $secondUser->id,
                'product_id' => $flow->id,
                'material_id' => $tpu90->id,
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
            ];
        }

        foreach ($designs as $payload) {
            ProductDesign::updateOrCreate(
                [
                    'user_id' => $payload['user_id'],
                    'product_id' => $payload['product_id'],
                    'material_id' => $payload['material_id'],
                    'color_name' => $payload['color_name'],
                    'size_eu' => $payload['size_eu'],
                ],
                $payload
            );
        }
    }
}
