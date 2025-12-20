<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

use App\Http\Requests\StoreProductVariantRequest;
use App\Http\Requests\UpdateProductVariantRequest;

class ProductVariantController extends Controller
{
    public function variants(Product $product){
        $product->load('mainImage');

        $variants = $product->variants()
            ->with(['material', 'mainImage'])
            ->latest()
            ->get();

        return view('admin.products.variants.index', compact('product', 'variants'));
    }

    //Vista de las variantes de producto
    public function variantsCreate(Product $product)
    {
        $materials = Material::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.products.variants.create', compact('product', 'materials'));
    }

    //Guarda la variante en la BD
    public function variantsStore(StoreProductVariantRequest $request, Product $product)
    {
        /*
         * Validación eliminada al crear las request
         *
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:64', 'unique:product_variants,sku'],
            'size_eu' => ['required', 'numeric'],
            'color_name' => ['required', 'string', 'max:50'],
            'material_id' => ['required', 'integer', 'exists:materials,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        */
        $validated = $request->validated();
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        $product->variants()->create($validated);
        return redirect()
            ->route('admin.products.variants.index', $product)
            ->with('status', 'Variante creada correctamente');
    }

    public function variantsEdit(Product $product, ProductVariant $variant)
    {
        $variant->load('mainImage');
        $materials = Material::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.products.variants.edit', compact('product', 'variant', 'materials'));
    }
    public function variantsUpdate(UpdateProductVariantRequest $request, Product $product, ProductVariant $variant)
    {
        abort_unless($variant->product_id === $product->id, 404);

        /*
         * Validación eliminada por crear las request
         *
        $validated = $request->validate([
            'material_id' => ['required', 'integer', 'exists:materials,id'],
            'sku' => ['required', 'string', 'max:64', 'unique:product_variants,sku,' . $variant->id],
            'size_eu' => ['required', 'numeric'],
            'color_name' => ['required', 'string', 'max:50'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        */
        $validated = $request->validated();
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        $variant->update($validated);

        return redirect()
            ->route('admin.products.variants.index', $product)
            ->with('status', 'Variante actualizada correctamente');
    }

    public function variantsDestroy(Product $product, ProductVariant $variant)
    {
        abort_unless($variant->product_id === $product->id, 404);

        $variant->delete(); // soft delete

        return redirect()
            ->route('admin.products.variants.index', $product)
            ->with('status', 'Variante eliminada correctamente');
    }
}
