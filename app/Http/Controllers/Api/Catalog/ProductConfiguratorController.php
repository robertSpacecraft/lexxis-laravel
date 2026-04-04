<?php

namespace App\Http\Controllers\Api\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Product;
use App\Support\ApiResponse;

class ProductConfiguratorController extends Controller
{
    public function options(Product $product)
    {
        abort_unless($product->is_active, 404);

        $product->load('mainImage');

        $activeVariants = $product->variants()
            ->where('is_active', true)
            ->with(['material:id,name,slug,material_type', 'mainImage'])
            ->get();

        $materials = $activeVariants
            ->pluck('material')
            ->filter()
            ->unique('id')
            ->values();

        if ($materials->isEmpty()) {
            $materials = Material::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'material_type']);
        }

        $colorsByMaterial = $activeVariants
            ->groupBy('material_id')
            ->map(function ($variantsForMaterial) {
                return $variantsForMaterial
                    ->filter(fn ($variant) => !empty($variant->color_name))
                    ->groupBy('color_name')
                    ->map(function ($variantsForColor, $colorName) {
                        $previewVariant = $variantsForColor->first();

                        return [
                            'name' => $colorName,
                            'preview_variant_id' => $previewVariant->id,
                            'preview_image' => $previewVariant->mainImage,
                        ];
                    })
                    ->values();
            });

        $sizes = $activeVariants
            ->pluck('size_eu')
            ->filter()
            ->map(fn ($size) => (float) $size)
            ->unique()
            ->sort()
            ->values();

        if ($sizes->isEmpty()) {
            $min = (int) config('lexxis_catalog.design_sizes.min_eu', 35);
            $max = (int) config('lexxis_catalog.design_sizes.max_eu', 46);
            $step = (int) config('lexxis_catalog.design_sizes.step', 1);

            $generated = [];
            for ($size = $min; $size <= $max; $size += $step) {
                $generated[] = (float) $size;
            }

            $sizes = collect($generated);
        }

        return ApiResponse::success([
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'base_price' => $product->base_price,
                'main_image' => $product->mainImage,
            ],
            'materials' => $materials,
            'colors_by_material' => $colorsByMaterial,
            'sizes' => $sizes,
            'preview_variants' => $activeVariants,
        ]);
    }
}
