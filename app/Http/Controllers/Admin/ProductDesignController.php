<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ProductDesignStatus;
use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Product;
use App\Models\ProductDesign;
use App\Models\User;
use Illuminate\Http\Request;

class ProductDesignController extends Controller
{
    public function globalIndex(Request $request)
    {
        $query = ProductDesign::query()
            ->with([
                'user:id,name,last_name,email',
                'product:id,name,slug',
                'material:id,name,slug,material_type',
            ])
            ->latest('id');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', (int) $request->input('user_id'));
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', (int) $request->input('product_id'));
        }

        if ($request->filled('q')) {
            $q = trim((string) $request->input('q'));

            $query->where(function ($sub) use ($q) {
                $sub->where('color_name', 'like', "%{$q}%")
                    ->orWhereHas('user', function ($userQuery) use ($q) {
                        $userQuery->where('name', 'like', "%{$q}%")
                            ->orWhere('last_name', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%");
                    })
                    ->orWhereHas('product', function ($productQuery) use ($q) {
                        $productQuery->where('name', 'like', "%{$q}%")
                            ->orWhere('slug', 'like', "%{$q}%");
                    })
                    ->orWhereHas('material', function ($materialQuery) use ($q) {
                        $materialQuery->where('name', 'like', "%{$q}%")
                            ->orWhere('slug', 'like', "%{$q}%");
                    });
            });
        }

        $designs = $query->paginate(20)->withQueryString();

        $products = Product::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $users = User::query()
            ->orderBy('name')
            ->get(['id', 'name', 'last_name', 'email']);

        return view('admin.product-designs.index', [
            'designs' => $designs,
            'products' => $products,
            'users' => $users,
            'statuses' => ProductDesignStatus::cases(),
            'filters' => $request->only(['status', 'user_id', 'product_id', 'q']),
        ]);
    }

    public function productIndex(Product $product)
    {
        $product->load('mainImage');

        $designs = ProductDesign::query()
            ->where('product_id', $product->id)
            ->with([
                'user:id,name,last_name,email',
                'material:id,name,slug,material_type',
            ])
            ->latest('id')
            ->paginate(20);

        return view('admin.products.designs.index', compact('product', 'designs'));
    }

    public function show(Product $product, ProductDesign $design)
    {
        abort_unless($design->product_id === $product->id, 404);

        $design->load([
            'user:id,name,last_name,email,phone',
            'product:id,name,slug,base_price,short_description',
            'material:id,name,slug,material_type,brand,supplier',
        ]);

        return view('admin.products.designs.show', [
            'product' => $product,
            'design' => $design,
        ]);
    }

    public function edit(Product $product, ProductDesign $design)
    {
        abort_unless($design->product_id === $product->id, 404);

        $materials = Material::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.products.designs.edit', [
            'product' => $product,
            'design' => $design,
            'materials' => $materials,
            'statuses' => ProductDesignStatus::cases(),
        ]);
    }

    public function update(Request $request, Product $product, ProductDesign $design)
    {
        abort_unless($design->product_id === $product->id, 404);

        $validated = $request->validate([
            'material_id' => ['required', 'integer', 'exists:materials,id'],
            'color_name' => ['required', 'string', 'max:50'],
            'size_eu' => ['required', 'numeric', 'min:1'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'in:' . implode(',', ProductDesignStatus::values())],
        ]);

        $pricingBreakdown = $design->pricing_breakdown ?? [];

        if ($validated['unit_price'] !== null) {
            $pricingBreakdown['admin_last_override'] = [
                'unit_price' => (float) $validated['unit_price'],
                'updated_at' => now()->toDateTimeString(),
            ];
        }

        $design->update([
            'material_id' => $validated['material_id'],
            'color_name' => $validated['color_name'],
            'size_eu' => $validated['size_eu'],
            'unit_price' => $validated['unit_price'],
            'status' => $validated['status'],
            'pricing_breakdown' => $pricingBreakdown,
        ]);

        return redirect()
            ->route('admin.products.designs.show', [$product, $design])
            ->with('success', 'Diseño actualizado correctamente.');
    }

    public function destroy(Product $product, ProductDesign $design)
    {
        abort_unless($design->product_id === $product->id, 404);

        $design->delete();

        return redirect()
            ->route('admin.products.designs.index', $product)
            ->with('success', 'Diseño eliminado correctamente.');
    }
}
