<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;

class productImageController extends Controller
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

    public function store(Request $request, Product $product){
        $validated = $request->validate([
           'image' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:4096'],
           'alt_text' => ['nullable', 'string', 'max:255'],
           'is_main' => ['nullable', 'boolean'],
        ]);

        $isMain = (bool) ($validated['is_main'] ?? false);

        //Si se marca la checkbox como principal se desmarca del resto
        if ($isMain){
           $product->images()->update(['is_main' => false]);
        }

        //Calculo el sort_order
        $nextOrder = ($product->images()->max('sort_order') ?? 0) + 1;

        //Guardo el archivo
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

    public function edit(){

    }

    public function update(){
        //
    }

    public function destroy(){
        //
    }

}
