<?php

namespace App\Http\Controllers\Api\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->where('is_active', true)
            ->with(['mainImage'])
            ->latest();

        if ($request->filled('q')) {
            $q = trim((string) $request->query('q'));

            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%")
                    ->orWhere('type', 'like', "%{$q}%");
            });
        }

        $products = $query->paginate(15);

        return ApiResponse::paginated($products);
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        $product->load([
            'mainImage',
            'variants' => function ($q) {
                $q->where('is_active', true)
                    ->with(['material', 'mainImage'])
                    ->latest();
            },
        ]);

        return ApiResponse::success($product);
    }
}
