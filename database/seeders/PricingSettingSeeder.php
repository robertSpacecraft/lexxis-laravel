<?php

namespace Database\Seeders;

use App\Models\PricingSetting;
use Illuminate\Database\Seeder;

class PricingSettingSeeder extends Seeder
{
    public function run(): void
    {
        PricingSetting::query()
            ->where('version', '!=', 'fdm-v3')
            ->update(['active' => false]);

        PricingSetting::query()->updateOrCreate(
            ['version' => 'fdm-v3'],
            [
                'active' => true,

                'material_cost_per_kg' => 25.00,
                'machine_cost_per_min' => 0.04,
                'setup_fee_per_job' => 5.00,

                'material_multiplier' => 1.20,
                'time_multiplier' => 1.15,
                'margin_multiplier' => 1.30,
                'risk_multiplier' => 1.10,
                'unreviewed_risk_multiplier' => 1.25,

                'infill_multipliers' => [
                    '5' => 0.60,
                    '15' => 1.00,
                    '40' => 1.60,
                ],
            ],
        );
    }
}
