<?php

namespace App\Services;

use App\Models\PricingSetting;
use App\Models\PrintJob;

class PrintJobPricingService
{
    public function quote(PrintJob $printJob, bool $withoutManualReview = false): array
    {
        $pricing = PricingSetting::active();
        abort_unless($pricing, 500, 'No hay PricingSetting activa.');

        $analysisSource = (string) ($printJob->analysis_source ?? 'unknown');
        $infill = (int) ($printJob->infill_percent ?? 15);

        $infillMultiplier = str_starts_with($analysisSource, 'gcode_')
            ? 1.0
            : $this->resolveInfillMultiplier((array) ($pricing->infill_multipliers ?? []), $infill);

        $materialG = (float) ($printJob->estimated_material_g ?? 0);
        $timeMin = (int) ($printJob->estimated_time_min ?? 0);
        $volumeCm3 = $printJob->estimated_volume_cm3 !== null
            ? (float) $printJob->estimated_volume_cm3
            : null;

        $materialCostPerG = ((float) $pricing->material_cost_per_kg) / 1000.0;
        $materialCost = $materialG * $materialCostPerG * (float) $pricing->material_multiplier * $infillMultiplier;
        $machineCost = $timeMin * (float) $pricing->machine_cost_per_min * (float) $pricing->time_multiplier;
        $setupFee = (float) $pricing->setup_fee_per_job;

        $technicalBase = ($materialCost + $machineCost + $setupFee) * (float) $pricing->risk_multiplier;

        if ($withoutManualReview) {
            $technicalBase *= (float) $pricing->unreviewed_risk_multiplier;
        }

        $unitPrice = round($technicalBase * (float) $pricing->margin_multiplier, 2);

        return [
            'estimated_material_g' => round($materialG, 2),
            'estimated_time_min' => $timeMin,
            'estimated_volume_cm3' => $volumeCm3 !== null ? round($volumeCm3, 2) : null,
            'analysis_source' => $analysisSource,
            'unit_price' => number_format($unitPrice, 2, '.', ''),
            'pricing_breakdown' => [
                'version' => (string) $pricing->version,
                'inputs' => [
                    'material_id' => (int) $printJob->material_id,
                    'technology' => (string) $printJob->technology,
                    'quantity' => (int) $printJob->quantity,
                    'infill_percent' => (int) $printJob->infill_percent,
                    'scale_percent' => (int) $printJob->scale_percent,
                ],
                'analysis' => [
                    'source' => $analysisSource,
                    'estimated_volume_cm3' => $volumeCm3 !== null ? round($volumeCm3, 2) : null,
                    'estimated_material_g' => round($materialG, 2),
                    'estimated_time_min' => $timeMin,
                ],
                'costs' => [
                    'material' => round($materialCost, 2),
                    'machine_time' => round($machineCost, 2),
                    'setup_fee' => round($setupFee, 2),
                    'technical_base_with_risk' => round($technicalBase, 2),
                ],
                'multipliers' => [
                    'material' => (float) $pricing->material_multiplier,
                    'time' => (float) $pricing->time_multiplier,
                    'margin' => (float) $pricing->margin_multiplier,
                    'risk' => (float) $pricing->risk_multiplier,
                    'unreviewed_risk' => $withoutManualReview ? (float) $pricing->unreviewed_risk_multiplier : 1.0,
                    'infill' => $infillMultiplier,
                ],
                'without_manual_review' => $withoutManualReview,
                'unit_price' => number_format($unitPrice, 2, '.', ''),
            ],
        ];
    }

    private function resolveInfillMultiplier(array $map, int $infillPercent): float
    {
        $key = (string) $infillPercent;

        if (isset($map[$key])) {
            return (float) $map[$key];
        }

        $normalized = array_map('intval', array_keys($map));
        sort($normalized);

        $closest = null;
        $distance = null;

        foreach ($normalized as $candidate) {
            $currentDistance = abs($candidate - $infillPercent);

            if ($distance === null || $currentDistance < $distance) {
                $distance = $currentDistance;
                $closest = $candidate;
            }
        }

        if ($closest !== null && isset($map[(string) $closest])) {
            return (float) $map[(string) $closest];
        }

        return 1.0;
    }
}
