<?php

namespace App\Http\Controllers\Api;

use App\Enums\ProductDesignStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductDesignRequest;
use App\Http\Requests\UpdateProductDesignRequest;
use App\Models\Product;
use App\Models\ProductDesign;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class ProductDesignController extends Controller
{
    public function index(Request $request)
    {
        $designs = ProductDesign::query()
            ->ownedBy($request->user()->id)
            ->with([
                'product:id,name,slug,base_price',
                'material:id,name,slug,material_type',
            ])
            ->orderByDesc('id')
            ->paginate(15);

        return ApiResponse::paginated($designs);
    }

    public function store(StoreProductDesignRequest $request)
    {
        $data = $request->validated();

        /** @var \App\Models\Product $product */
        $product = Product::query()->findOrFail($data['product_id']);
        abort_unless($product->is_active, 422, 'El producto no está disponible.');

        $design = ProductDesign::query()->create([
            'user_id' => $request->user()->id,
            'product_id' => $data['product_id'],
            'material_id' => $data['material_id'],
            'color_name' => $data['color_name'],
            'size_eu' => $data['size_eu'],
            'unit_price' => $product->base_price,
            'pricing_breakdown' => [
                'source' => 'product_base_price',
                'product_base_price' => $product->base_price,
                'notes' => 'Precio base inicial. Pendiente de lógica avanzada de personalización.',
            ],
            'status' => ProductDesignStatus::Draft,
        ]);

        $design->load([
            'product:id,name,slug,base_price',
            'material:id,name,slug,material_type',
        ]);

        return ApiResponse::created(
            data: $design,
            message: 'Diseño creado correctamente.'
        );
    }

    public function show(Request $request, ProductDesign $productDesign)
    {
        $this->ensureOwnership($request, $productDesign);

        $productDesign->load([
            'product:id,name,slug,base_price',
            'material:id,name,slug,material_type',
        ]);

        return ApiResponse::success($productDesign);
    }

    public function update(UpdateProductDesignRequest $request, ProductDesign $productDesign)
    {
        $this->ensureOwnership($request, $productDesign);
        $this->ensureDraft($productDesign);

        $data = $request->validated();

        $productDesign->update($data);

        $productDesign->refresh()->load([
            'product:id,name,slug,base_price',
            'material:id,name,slug,material_type',
        ]);

        return ApiResponse::success(
            data: $productDesign,
            message: 'Diseño actualizado correctamente.'
        );
    }

    public function destroy(Request $request, ProductDesign $productDesign)
    {
        $this->ensureOwnership($request, $productDesign);
        $this->ensureDraft($productDesign);

        $productDesign->delete();

        return ApiResponse::success(
            data: null,
            message: 'Diseño eliminado correctamente.'
        );
    }

    private function ensureOwnership(Request $request, ProductDesign $productDesign): void
    {
        abort_unless((int) $productDesign->user_id === (int) $request->user()->id, 403);
    }

    private function ensureDraft(ProductDesign $productDesign): void
    {
        abort_unless(
            $productDesign->isDraft(),
            422,
            'Este diseño ya no puede modificarse en su estado actual.'
        );
    }
}
