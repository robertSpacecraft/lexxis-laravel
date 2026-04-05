<?php

namespace App\Http\Controllers\Api;

use App\Enums\CartItemType;
use App\Enums\ProductDesignStatus;
use App\Enums\PrintJobStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Carts\AddPrintJobToCartRequest;
use App\Http\Requests\Carts\AddProductDesignToCartRequest;
use App\Http\Requests\Carts\AddProductVariantToCartRequest;
use App\Http\Requests\Carts\UpdateCartItemQuantityRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\PrintJob;
use App\Models\ProductDesign;
use App\Models\ProductVariant;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function show(Request $request)
    {
        $cart = $this->loadActiveCart($request->user()->id);

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
                    'type' => CartItemType::ProductVariant,
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
                'type' => CartItemType::ProductVariant,
                'quantity' => $quantityToAdd,
                'unit_price' => $unitPrice,
                'subtotal' => $this->moneyMul($unitPrice, $quantityToAdd),
                'metadata' => [
                    'source' => 'product_variant',
                ],
            ]);
        });

        $cart = $this->loadActiveCart($userId);

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
                    'type' => CartItemType::ProductDesign,
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
                'type' => CartItemType::ProductDesign,
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

        $cart = $this->loadActiveCart($userId);

        return ApiResponse::success(
            data: $cart,
            message: 'Diseño añadido al carrito.'
        );
    }

    public function addPrintJob(
        AddPrintJobToCartRequest $request,
        PrintJob $printJob
    ) {
        $userId = auth()->id();

        abort_unless((int) $printJob->user_id === (int) $userId, 403);

        abort_unless(
            ($printJob->status?->value ?? (string) $printJob->status) === PrintJobStatus::Priced->value,
            422,
            'Este trabajo de impresión no está listo para añadirse al carrito.'
        );

        $quantity = $request->validatedQuantity();
        $unitPrice = (string) ($printJob->unit_price ?? '0.00');

        $cart = Cart::query()->firstOrCreate([
            'user_id' => $userId,
            'status' => 'active',
        ]);

        DB::transaction(function () use ($cart, $printJob, $quantity, $unitPrice) {
            $lockedJob = PrintJob::query()
                ->whereKey($printJob->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                ($lockedJob->status?->value ?? (string) $lockedJob->status) === PrintJobStatus::Priced->value,
                422,
                'Este trabajo de impresión no se puede añadir al carrito.'
            );

            $existing = CartItem::query()
                ->where('cart_id', $cart->id)
                ->where('print_job_id', $lockedJob->id)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                abort(422, 'Este trabajo de impresión ya está en el carrito.');
            }

            CartItem::query()->create([
                'cart_id' => $cart->id,
                'product_variant_id' => null,
                'product_design_id' => null,
                'print_job_id' => $lockedJob->id,
                'type' => CartItemType::PrintJob,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $this->moneyMul($unitPrice, $quantity),
                'metadata' => [
                    'source' => 'print_job',
                    'material_id' => $lockedJob->material_id,
                    'color_name' => $lockedJob->color_name,
                    'technology' => $lockedJob->technology,
                    'estimated_material_g' => $lockedJob->estimated_material_g,
                    'estimated_time_min' => $lockedJob->estimated_time_min,
                ],
            ]);

            $lockedJob->status = PrintJobStatus::InCart;
            $lockedJob->save();
        });

        $cart = $this->loadActiveCart($userId);

        return ApiResponse::success(
            data: $cart,
            message: 'Trabajo de impresión añadido al carrito.'
        );
    }

    public function updateItemQuantity(
        UpdateCartItemQuantityRequest $request,
        CartItem $cartItem
    ) {
        $this->ensureOwnership($cartItem);

        abort_unless(
            $cartItem->isProductVariant(),
            422,
            'Solo se puede modificar la cantidad de variantes de producto.'
        );

        $newQuantity = $request->validatedQuantity();
        $unitPrice = (string) $cartItem->unit_price;

        $cartItem->update([
            'quantity' => $newQuantity,
            'subtotal' => $this->moneyMul($unitPrice, $newQuantity),
        ]);

        $cart = $this->loadActiveCart(auth()->id());

        return ApiResponse::success(
            data: $cart,
            message: 'Cantidad actualizada correctamente.'
        );
    }

    public function destroyItem(CartItem $cartItem)
    {
        $this->ensureOwnership($cartItem);

        DB::transaction(function () use ($cartItem) {
            $designId = $cartItem->product_design_id;

            $cartItem->delete();

            if ($designId) {
                $design = ProductDesign::query()->find($designId);

                if ($design && ($design->status?->value ?? (string) $design->status) === ProductDesignStatus::InCart->value) {
                    $design->status = ProductDesignStatus::Draft;
                    $design->save();
                }
            }

            $printJobId = $cartItem->print_job_id;

            if ($printJobId) {
                $job = PrintJob::query()->find($printJobId);

                if ($job && ($job->status?->value ?? (string) $job->status) === PrintJobStatus::InCart->value) {
                    $job->status = PrintJobStatus::Priced;
                    $job->save();
                }
            }
        });

        $cart = $this->loadActiveCart(auth()->id());

        return ApiResponse::success(
            data: $cart,
            message: 'Item eliminado del carrito.'
        );
    }

    private function ensureOwnership(CartItem $cartItem): void
    {
        abort_unless(
            (int) $cartItem->cart?->user_id === (int) auth()->id(),
            403
        );
    }

    private function loadActiveCart(int $userId): ?Cart
    {
        return Cart::query()
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->with([
                'items.productVariant.product:id,name,slug',
                'items.productVariant.mainImage',
                'items.productDesign.product:id,name,slug',
                'items.productDesign.material:id,name,slug,material_type',
                'items.printJob.material:id,name,slug,material_type',
                'items.printJob.printFile:id,original_name',
            ])
            ->latest('id')
            ->first();
    }

    private function moneyMul(string|float $unitPrice, int $quantity): string
    {
        $value = (float) $unitPrice * $quantity;

        return number_format(round($value, 2), 2, '.', '');
    }
}
