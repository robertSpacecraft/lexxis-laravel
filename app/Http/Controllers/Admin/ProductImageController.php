<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProductImageRequest;
use App\Http\Requests\UpdateProductImageRequest;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    public function index(Product $product){
        $images = $product->images()
            ->orderBy('sort_order')
            ->get();
        return view('admin.products.images.index', compact('product', 'images'));
    }

    public function create(Product $product){
        return view('admin.products.images.create', compact('product'));
    }

    public function store(StoreProductImageRequest $request, Product $product)
    {
        $validated = $request->validated();

        $isMain = (bool) ($validated['is_main'] ?? false);

        if ($isMain) {
            $product->images()->update(['is_main' => false]);
        }

        $nextOrder = ($product->images()->max('sort_order') ?? 0) + 1;

        $path = $request->file('image')->store('products/' . $product->id, 'public');

        $product->images()->create([
            'path' => $path,
            'alt_text' => $validated['alt_text'] ?? null,
            'is_main' => $isMain,
            'sort_order' => $nextOrder,
        ]);

        return redirect()
            ->route('admin.products.images.index', $product)
            ->with('status', 'Imagen subida correctamente');
    }

    public function edit(Product $product, ProductImage $image){
        abort_unless($image->product_id === $product->id, 404);
        return view('admin.products.images.edit', compact('product', 'image'));

    }

    public function update(UpdateProductImageRequest $request, Product $product, ProductImage $image)
    {
        abort_unless($image->product_id === $product->id, 404);

        $validated = $request->validated();

        //Reemplazo de archivo
        if ($request->hasFile('image')) {
            if ($image->path && Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            }

            $image->path = $request->file('image')
                ->store('products/' . $product->id, 'public');
        }

        //Texto alternativo
        $image->alt_text = $validated['alt_text'] ?? null;

        // 3) Imagen principal (solo si se marca)
        $isMain = (bool) ($validated['is_main'] ?? false);
        if ($isMain) {
            $product->images()->whereKeyNot($image->id)->update(['is_main' => false]);
            $image->is_main = true;
        }

        $image->save();

        return redirect()
            ->route('admin.products.images.index', $product)
            ->with('status', 'Imagen actualizada correctamente');
    }


    public function destroy(Product $product, ProductImage $image)
    {
        abort_unless($image->product_id === $product->id, 404);

        // Borrado físico del archivo
        if ($image->path && Storage::disk('public')->exists($image->path)) {
            Storage::disk('public')->delete($image->path);
        }

        $image->delete();

        return redirect()
            ->route('admin.products.images.index', $product)
            ->with('status', 'Imagen eliminada correctamente');
    }


}
