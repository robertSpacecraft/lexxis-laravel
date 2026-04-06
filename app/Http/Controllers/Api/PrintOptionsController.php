<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Support\ApiResponse;
use App\Support\PrintJobOptions;

class PrintOptionsController extends Controller
{
    public function index()
    {
        $materials = Material::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'slug',
                'material_type',
                'brand',
                'supplier',
                'shore_scale',
                'shore_value',
                'description',
                'is_active',
            ]);

        return ApiResponse::success([
            'materials' => $materials,
            'technologies' => PrintJobOptions::technologyOptions(),
            'infill_percent_options' => PrintJobOptions::infillPercents(),
            'quantity' => [
                'min' => PrintJobOptions::QUANTITY_MIN,
                'max' => PrintJobOptions::QUANTITY_MAX,
                'default' => PrintJobOptions::DEFAULT_QUANTITY,
            ],
            'scale_percent' => [
                'min' => PrintJobOptions::SCALE_PERCENT_MIN,
                'max' => PrintJobOptions::SCALE_PERCENT_MAX,
                'default' => PrintJobOptions::DEFAULT_SCALE_PERCENT,
            ],
            'defaults' => PrintJobOptions::defaults(),
            'color_name' => [
                'type' => 'free_text',
                'nullable' => true,
                'max_length' => 80,
                'note' => 'Actualmente el backend no mantiene un catálogo cerrado de colores por material.',
            ],
        ]);
    }
}
