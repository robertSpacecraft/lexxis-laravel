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
            ->with(['mainImage'])
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
        $product->load('mainImage');
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

}
