<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(DefaultMaterialSeeder::class);
        $this->call(CountriesSeeder::class);
        $this->call(SpainProvincesSeeder::class);
        $this->call(SpainCitiesSeeder::class);
        $this->call(DevStreetsSeeder::class);
        $this->call(DemoUsersSeeder::class);
        $this->call(DemoAddressesSeeder::class);
        $this->call(DemoProductsSeeder::class);
        $this->call(DemoProductVariantsSeeder::class);
        $this->call(DemoProductDesignsSeeder::class);
        $this->call(DemoPrintFilesAndJobsSeeder::class);
        $this->call(DevOrdersSeeder::class);
        $this->call(PricingSettingSeeder::class);
    }
}
