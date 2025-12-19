<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Http\Requests\StoreProductVariantImageRequest;
use App\Models\ProductVariantImage;

class ProductVariantImageController extends Controller
{
    public function index(Product $product, ProductVariant $variant)
    {
        abort_unless($variant->product_id === $product->id, 404);

        $images = $variant->images()
            ->orderBy('sort_order')
            ->get();

        return view('admin.products.variants.images.index', compact('product', 'variant', 'images'));
    }

    public function create(Product $product, ProductVariant $variant)
    {
        abort_unless($variant->product_id === $product->id, 404);

        return view('admin.products.variants.images.create', compact('product', 'variant'));
    }

    public function store(StoreProductVariantImageRequest $request, Product $product, ProductVariant $variant)
    {
        abort_unless($variant->product_id === $product->id, 404);

        $validated = $request->validated();
        $isMain = (bool) ($validated['is_main'] ?? false);

        if ($isMain) {
            $variant->images()->update(['is_main' => false]);
        }

        $nextOrder = ($variant->images()->max('sort_order') ?? 0) + 1;

        $path = $request->file('image')->store(
            'products/' . $product->id . '/variants/' . $variant->id,
            'public'
        );

        $variant->images()->create([
            'path' => $path,
            'alt_text' => $validated['alt_text'] ?? null,
            'is_main' => $isMain,
            'sort_order' => $nextOrder,
        ]);

        return redirect()
            ->route('admin.products.variants.images.index', [$product, $variant])
            ->with('status', 'Imagen de variante subida correctamente');
    }

}
