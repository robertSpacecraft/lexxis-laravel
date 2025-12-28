<?php

namespace App\Services;

use App\Models\PricingSetting;
use App\Models\PrintJob;

class PrintJobPricingService
{
    public function quote(PrintJob $printJob, int $infill = 15): array
    {
        $pricing = PricingSetting::active();
        abort_unless($pricing, 500, 'No hay PricingSetting activa.');

        $infillMultipliers = (array) ($pricing->infill_multipliers ?? []);
        $infillMultiplier = (float) ($infillMultipliers[(string) $infill] ?? 1.0);

        //MVP: si no hay estimaciones todavía, uso placeholders seguros
        //En el siguiente paso conecto esto con métricas reales (STL/OBJ)
        $materialG = (float) ($printJob->estimated_material_g ?? 0);
        $timeMin   = (int) ($printJob->estimated_time_min ?? 0);

        $materialCostPerG = ((float) $pricing->material_cost_per_kg) / 1000.0;
        $materialCost = $materialG * $materialCostPerG * (float) $pricing->material_multiplier * $infillMultiplier;

        $machineCost = $timeMin * (float) $pricing->machine_cost_per_min * (float) $pricing->time_multiplier * $infillMultiplier;

        $setupFee = (float) $pricing->setup_fee_per_job;

        $base = $materialCost + $machineCost + $setupFee;
        $unitPrice = $base * (float) $pricing->margin_multiplier;

        $unitPrice = round($unitPrice, 2);

        return [
            'estimated_material_g' => round($materialG, 2),
            'estimated_time_min' => $timeMin,
            'unit_price' => number_format($unitPrice, 2, '.', ''),
            'pricing_breakdown' => [
                'version' => (string) $pricing->version,
                'inputs' => [
                    'material_id' => (int) $printJob->material_id,
                    'technology' => (string) $printJob->technology,
                    'infill' => $infill,
                    'quantity' => (int) $printJob->quantity,
                ],
                'estimation' => [
                    'material_g' => round($materialG, 2),
                    'time_min' => $timeMin,
                ],
                'costs' => [
                    'material' => round($materialCost, 2),
                    'machine_time' => round($machineCost, 2),
                    'setup_fee' => round($setupFee, 2),
                ],
                'multipliers' => [
                    'material' => (float) $pricing->material_multiplier,
                    'time' => (float) $pricing->time_multiplier,
                    'margin' => (float) $pricing->margin_multiplier,
                    'infill' => $infillMultiplier,
                ],
                'unit_price' => number_format($unitPrice, 2, '.', ''),
            ],
        ];
    }
}

