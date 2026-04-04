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
        $flow = Product::query()->where('slug', 'lexxis-flow')->first();
        $urban = Product::query()->where('slug', 'lexxis-urban')->first();

        $tpu95 = Material::query()->where('slug', 'tpu-flex-95a')->first();
        $tpu90 = Material::query()->where('slug', 'tpu-comfort-90a')->first();
        $pla = Material::query()->where('slug', 'pla-display')->first();

        if (!$flow || !$urban || !$tpu95 || !$tpu90 || !$pla) {
            $this->command?->warn('Faltan productos o materiales para sembrar variantes demo.');
            return;
        }

        $variants = [
            [
                'product_id' => $flow->id,
                'material_id' => $tpu95->id,
                'sku' => 'FLOW-BLK-42-95A',
                'size_eu' => 42.0,
                'color_name' => 'Negro',
                'price' => 92.90,
                'stock' => 5,
                'is_active' => true,
            ],
            [
                'product_id' => $flow->id,
                'material_id' => $tpu90->id,
                'sku' => 'FLOW-SND-41-90A',
                'size_eu' => 41.0,
                'color_name' => 'Arena',
                'price' => 94.90,
                'stock' => 2,
                'is_active' => true,
            ],
            [
                'product_id' => $urban->id,
                'material_id' => $tpu95->id,
                'sku' => 'URBAN-WHT-43-95A',
                'size_eu' => 43.0,
                'color_name' => 'Blanco',
                'price' => 104.90,
                'stock' => 3,
                'is_active' => true,
            ],
            [
                'product_id' => $urban->id,
                'material_id' => $pla->id,
                'sku' => 'URBAN-RED-42-PLA',
                'size_eu' => 42.0,
                'color_name' => 'Rojo',
                'price' => 109.90,
                'stock' => 1,
                'is_active' => true,
            ],
        ];

        foreach ($variants as $payload) {
            ProductVariant::updateOrCreate(
                ['sku' => $payload['sku']],
                $payload
            );
        }
    }
}
