<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Material;

class ProductVariantSeeder extends Seeder
{
    public function run(): void
    {
        $materials = Material::all();
        $products = Product::all();

        foreach ($products as $product) {
            foreach ($materials->take(2) as $material) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'material_id' => $material->id,
                    'color' => 'black',
                    'size' => 42,
                    'price' => rand(10, 30),
                ]);
            }
        }
    }
}
