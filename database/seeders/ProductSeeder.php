<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'name' => 'Sneaker Alpha',
            'description' => 'Zapatilla impresa en 3D',
            'base_price' => 50,
        ]);

        Product::create([
            'name' => 'Sneaker Beta',
            'description' => 'Zapatilla flexible',
            'base_price' => 60,
        ]);
    }
}
