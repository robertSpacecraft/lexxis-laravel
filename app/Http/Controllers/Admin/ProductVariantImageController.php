<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Http\Requests\StoreProductVariantImageRequest;
use Illuminate\Support\Facades\Storage;
use App\Models\ProductVariantImage;
use App\Http\Requests\UpdateProductVariantImageRequest;

class ProductVariantImageController extends Controller
{
    public function index(Product $product, ProductVariant $variant)
    {
        abort_unless($variant->product_id === $product->id, 404);

        $images = $variant->images()
            ->orderBy('is_main', 'desc')
            ->orderBy('sort_order')
            ->get();

        return view('admin.products.variants.images.index', compact('product', 'variant', 'images'));
    }

    public function create(Product $product, ProductVariant $variant)
    {
        abort_unless($variant->product_id === $product->id, 404);

        return view('admin.products.variants.images.create', compact('product', 'variant'));
    }

    public function edit(Product $product, ProductVariant $variant, ProductVariantImage $image)
    {
        abort_unless($variant->product_id === $product->id, 404);
        abort_unless($image->product_variant_id === $variant->id, 404);

        return view('admin.products.variants.images.edit', compact('product', 'variant', 'image'));
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

    public function update(UpdateProductVariantImageRequest $request, Product $product, ProductVariant $variant, ProductVariantImage $image)
    {
        abort_unless($variant->product_id === $product->id, 404);
        abort_unless($image->product_variant_id === $variant->id, 404);

        $validated = $request->validated();

        // Reemplazo de archivo (opcional)
        if ($request->hasFile('image')) {
            if ($image->path && Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            }

            $image->path = $request->file('image')
                ->store('products/' . $product->id . '/variants/' . $variant->id, 'public');
        }

        // Alt text
        $image->alt_text = $validated['alt_text'] ?? null;

        // Principal
        $isMain = (bool) ($validated['is_main'] ?? false);
        if ($isMain) {
            $variant->images()->whereKeyNot($image->id)->update(['is_main' => false]);
            $image->is_main = true;
        }

        $image->save();

        return redirect()
            ->route('admin.products.variants.images.index', [$product, $variant])
            ->with('status', 'Imagen de variante actualizada correctamente');
    }

    public function destroy(Product $product, ProductVariant $variant, ProductVariantImage $image)
    {
        abort_unless($variant->product_id === $product->id, 404);
        abort_unless($image->product_variant_id === $variant->id, 404);

        if ($image->path && Storage::disk('public')->exists($image->path)) {
            Storage::disk('public')->delete($image->path);
        }

        $image->delete();

        return redirect()
            ->route('admin.products.variants.images.index', [$product, $variant])
            ->with('status', 'Imagen de variante eliminada correctamente');
    }

}
