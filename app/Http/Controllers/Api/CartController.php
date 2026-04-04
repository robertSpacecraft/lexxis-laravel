<?php

namespace App\Http\Controllers\Api;

use App\Enums\ProductDesignStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Carts\AddProductDesignToCartRequest;
use App\Http\Requests\Carts\AddProductVariantToCartRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductDesign;
use App\Models\ProductVariant;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function show(Request $request)
    {
        $cart = Cart::query()
            ->where('user_id', $request->user()->id)
            ->where('status', 'active')
            ->with([
                'items.productVariant.product:id,name,slug',
                'items.productVariant.mainImage',
                'items.productDesign.product:id,name,slug',
                'items.productDesign.material:id,name,slug,material_type',
            ])
            ->latest('id')
            ->first();

        return ApiResponse::success($cart);
    }

    public function addProductVariant(
        AddProductVariantToCartRequest $request,
        ProductVariant $variant
    ) {
        abort_unless($variant->is_active, 422, 'La variante no está disponible.');

        $userId = auth()->id();
        $quantityToAdd = (int) $request->validated()['quantity'];
        $unitPrice = (string) ($variant->price ?? $variant->product?->base_price ?? '0.00');

        $cart = Cart::query()->firstOrCreate(
            [
                'user_id' => $userId,
                'status' => 'active',
            ],
            []
        );

        DB::transaction(function () use ($cart, $variant, $quantityToAdd, $unitPrice) {
            $item = CartItem::query()
                ->where('cart_id', $cart->id)
                ->where('product_variant_id', $variant->id)
                ->whereNull('product_design_id')
                ->whereNull('print_job_id')
                ->lockForUpdate()
                ->first();

            if ($item) {
                $newQty = $item->quantity + $quantityToAdd;

                $item->update([
                    'quantity' => $newQty,
                    'unit_price' => $unitPrice,
                    'subtotal' => $this->moneyMul($unitPrice, $newQty),
                ]);

                return;
            }

            CartItem::query()->create([
                'cart_id' => $cart->id,
                'product_variant_id' => $variant->id,
                'product_design_id' => null,
                'print_job_id' => null,
                'quantity' => $quantityToAdd,
                'unit_price' => $unitPrice,
                'subtotal' => $this->moneyMul($unitPrice, $quantityToAdd),
                'metadata' => [
                    'source' => 'product_variant',
                ],
            ]);
        });

        $cart->load([
            'items.productVariant.product:id,name,slug',
            'items.productVariant.mainImage',
            'items.productDesign.product:id,name,slug',
            'items.productDesign.material:id,name,slug,material_type',
        ]);

        return ApiResponse::success(
            data: $cart,
            message: 'Variante añadida al carrito.'
        );
    }

    public function addProductDesign(
        AddProductDesignToCartRequest $request,
        ProductDesign $productDesign
    ) {
        $userId = auth()->id();

        abort_unless((int) $productDesign->user_id === (int) $userId, 403);
        abort_unless($productDesign->isDraft(), 422, 'Este diseño no se puede añadir al carrito en su estado actual.');

        $quantityToAdd = $request->validatedQuantity();
        $unitPrice = (string) ($productDesign->unit_price ?? '0.00');

        $cart = Cart::query()->firstOrCreate(
            [
                'user_id' => $userId,
                'status' => 'active',
            ],
            []
        );

        DB::transaction(function () use ($cart, $productDesign, $quantityToAdd, $unitPrice) {
            $lockedDesign = ProductDesign::query()
                ->whereKey($productDesign->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless($lockedDesign->isDraft(), 422, 'Este diseño no se puede añadir al carrito en su estado actual.');

            $existing = CartItem::query()
                ->where('cart_id', $cart->id)
                ->where('product_design_id', $lockedDesign->id)
                ->whereNull('product_variant_id')
                ->whereNull('print_job_id')
                ->lockForUpdate()
                ->first();

            if ($existing) {
                $newQty = $existing->quantity + $quantityToAdd;

                $existing->update([
                    'quantity' => $newQty,
                    'unit_price' => $unitPrice,
                    'subtotal' => $this->moneyMul($unitPrice, $newQty),
                ]);

                return;
            }

            CartItem::query()->create([
                'cart_id' => $cart->id,
                'product_variant_id' => null,
                'product_design_id' => $lockedDesign->id,
                'print_job_id' => null,
                'quantity' => $quantityToAdd,
                'unit_price' => $unitPrice,
                'subtotal' => $this->moneyMul($unitPrice, $quantityToAdd),
                'metadata' => [
                    'source' => 'product_design',
                    'material_id' => $lockedDesign->material_id,
                    'color_name' => $lockedDesign->color_name,
                    'size_eu' => $lockedDesign->size_eu,
                ],
            ]);

            $lockedDesign->status = ProductDesignStatus::InCart;
            $lockedDesign->save();
        });

        $cart->load([
            'items.productVariant.product:id,name,slug',
            'items.productVariant.mainImage',
            'items.productDesign.product:id,name,slug',
            'items.productDesign.material:id,name,slug,material_type',
        ]);

        return ApiResponse::success(
            data: $cart,
            message: 'Diseño añadido al carrito.'
        );
    }

    private function moneyMul(string|float $unitPrice, int $quantity): string
    {
        $value = (float) $unitPrice * $quantity;

        return number_format(round($value, 2), 2, '.', '');
    }
}
