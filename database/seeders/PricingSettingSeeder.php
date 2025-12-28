<?php

namespace Database\Seeders;

use App\Models\PricingSetting;
use Illuminate\Database\Seeder;

class PricingSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Desactivo cualquier tarifa previa por seguridad
        PricingSetting::query()->update(['active' => false]);

        PricingSetting::query()->create([
            'version' => 'fdm-v1',
            'active' => true,

            //Costes base
            'material_cost_per_kg' => 25.00,      // €/kg (PLA medio)
            'machine_cost_per_min' => 0.04,       // €/min (~2.40 €/h)
            'setup_fee_per_job'    => 5.00,       // coste fijo por job

            //Multiplicadores
            'material_multiplier' => 1.20,
            'time_multiplier'     => 1.15,
            'margin_multiplier'   => 1.30,

            //Infill (relleno)
            'infill_multipliers' => [
                '5'  => 0.6,
                '15' => 1.0,
                '40' => 1.6,
            ],
        ]);
    }
}
