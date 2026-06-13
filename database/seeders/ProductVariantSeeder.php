<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductVariantSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(DemoProductVariantsSeeder::class);
    }
}
