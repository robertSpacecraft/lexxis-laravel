<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Support\ApiResponse;

class MaterialController extends Controller
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
                'shore_a',
                'shore_scale',
                'shore_value',
                'description',
                'is_active',
            ]);

        return ApiResponse::success($materials);
    }
}
