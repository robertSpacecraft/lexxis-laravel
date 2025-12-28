<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingSetting extends Model
{
    protected $fillable = [
        'version',
        'active',
        'material_cost_per_kg',
        'machine_cost_per_min',
        'setup_fee_per_job',
        'material_multiplier',
        'time_multiplier',
        'margin_multiplier',
        'infill_multipliers',
    ];

    protected $casts = [
        'active' => 'boolean',
        'material_cost_per_kg' => 'decimal:2',
        'machine_cost_per_min' => 'decimal:4',
        'setup_fee_per_job' => 'decimal:2',
        'material_multiplier' => 'decimal:2',
        'time_multiplier' => 'decimal:2',
        'margin_multiplier' => 'decimal:2',
        'infill_multipliers' => 'array',
    ];

    public static function active(): ?self
    {
        return static::query()->where('active', true)->first();
    }
}
