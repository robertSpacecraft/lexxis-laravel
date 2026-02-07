<?php

namespace App\Http\Controllers\Api\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;

class ProductVariantController extends Controller
{
    public function index(Product $product)
    {
        abort_unless($product->is_active, 404);

        $variants = $product->variants()
            ->where('is_active', true)
            ->with(['material', 'mainImage'])
            ->latest()
            ->get();

        return response()->json([
            'data' => $variants,
        ]);
    }

    public function show(Product $product, ProductVariant $variant)
    {
        abort_unless($product->is_active, 404);
        abort_unless($variant->product_id === $product->id, 404);
        abort_unless($variant->is_active, 404);

        $variant->load([
            'material',
            'mainImage',
            'images' => function ($q) {
                $q->orderBy('sort_order');
            },
        ]);

        return response()->json([
            'data' => $variant,
        ]);
    }
}
