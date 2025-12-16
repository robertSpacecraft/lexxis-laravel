<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Material;
use App\Models\ProductVariant;
use Illuminate\support\Str;

class ProductController extends Controller
{

    //vista de productos
    public function index(){
        $products = Product::query()
            ->withCount('variants')
            ->latest()
            ->get();

        return view('admin.products.index', compact("products"));
    }

    public function create(){
        return view('admin.products.create');
    }

    public function store(Request $request){
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        //para la checkbox
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);
        $validated['slug'] = str::slug($validated['name']);
        $validated['type'] = 'footwear';

        Product::create($validated);

        return redirect()->route('admin.products.index')->with('status', 'Producto creado correctamente');
    }

    public function edit(Product $product){
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product){
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);
        $validated['slug'] = str::slug($validated['name']);

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('status', 'Producto actualizado correctamente');
    }
    public function destroy(Product $product)
    {
        $product->delete(); // soft delete

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Producto eliminado correctamente');
    }


    //Manda las variantes de porductos a la vista
    public function variants(Product $product){
        $variants = $product->variants()
            ->with('material')
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
    public function variantsStore(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:64', 'unique:product_variants,sku'],
            'size_eu' => ['required', 'numeric'],
            'color_name' => ['required', 'string', 'max:50'],
            'material_id' => ['required', 'integer', 'exists:materials,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        $product->variants()->create($validated);
        return redirect()
            ->route('admin.products.variants.index', $product)
            ->with('status', 'Variante creada correctamente');
    }

    public function variantsEdit(Product $product, ProductVariant $variant)
    {
        $materials = Material::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.products.variants.edit', compact('product', 'variant', 'materials'));
    }
    public function variantsUpdate(Request $request, Product $product, ProductVariant $variant)
    {
        abort_unless($variant->product_id === $product->id, 404);

        $validated = $request->validate([
            'material_id' => ['required', 'integer', 'exists:materials,id'],
            'sku' => ['required', 'string', 'max:64', 'unique:product_variants,sku,' . $variant->id],
            'size_eu' => ['required', 'numeric'],
            'color_name' => ['required', 'string', 'max:50'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

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
