<?php

namespace App\Http\Controllers\Api;

use App\Enums\ProductDesignStatus;
use App\Enums\PrintJobStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Carts\CheckoutCartRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PrintJob;
use App\Models\ProductDesign;
use App\Models\ProductVariant;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::query()
            ->where('user_id', $request->user()->id)
            ->with([
                'items.productVariant.product:id,name,slug',
                'items.productDesign.product:id,name,slug',
                'items.printJob.printFile:id,original_name',
            ])
            ->latest('id')
            ->paginate(15);

        return ApiResponse::paginated($orders);
    }

    public function show(Request $request, Order $order)
    {
        abort_unless((int) $order->user_id === (int) $request->user()->id, 403);

        $order->load([
            'shippingAddress.street.city.province.country',
            'billingAddress.street.city.province.country',
            'items.productVariant.product:id,name,slug',
            'items.productVariant.mainImage',
            'items.productDesign.product:id,name,slug',
            'items.productDesign.material:id,name,slug,material_type',
            'items.printJob.material:id,name,slug,material_type',
            'items.printJob.printFile:id,original_name',
        ]);

        return ApiResponse::success($order);
    }

    public function checkout(CheckoutCartRequest $request)
    {
        $user = $request->user();

        $cart = Cart::query()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->with([
                'items.productVariant.product',
                'items.productDesign',
                'items.printJob',
            ])
            ->latest('id')
            ->first();

        abort_unless($cart, 422, 'No hay carrito activo.');
        abort_unless($cart->items->count() > 0, 422, 'El carrito está vacío.');

        return DB::transaction(function () use ($cart, $user, $request) {
            $shippingAddressId = $request->shippingAddressId();
            $billingAddressId = $request->billingAddressId();

            if ($shippingAddressId) {
                abort_unless(
                    $user->addresses()->whereKey($shippingAddressId)->exists(),
                    422,
                    'La dirección de envío no es válida.'
                );
            } else {
                $shippingAddressId = $user->addresses()->value('id');
            }

            if ($billingAddressId) {
                abort_unless(
                    $user->addresses()->whereKey($billingAddressId)->exists(),
                    422,
                    'La dirección de facturación no es válida.'
                );
            } else {
                $billingAddressId = $shippingAddressId;
            }

            abort_unless($shippingAddressId, 422, 'No hay dirección de envío disponible.');

            $order = Order::query()->create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . now()->format('YmdHis') . '-' . $user->id,
                'status' => 'pending',
                'payment_status' => 'pending',
                'shipping_address_id' => $shippingAddressId,
                'billing_address_id' => $billingAddressId,
                'subtotal' => $cart->items->sum('subtotal'),
                'tax' => 0,
                'shipping_cost' => 0,
                'total' => $cart->items->sum('subtotal'),
                'payment_method' => $request->paymentMethod(),
                'notes' => $request->notes(),
                'placed_at' => now(),
            ]);

            foreach ($cart->items as $item) {
                if ($item->product_design_id) {
                    $design = ProductDesign::query()
                        ->lockForUpdate()
                        ->find($item->product_design_id);

                    abort_unless(
                        $design && ($design->status?->value ?? (string) $design->status) === ProductDesignStatus::InCart->value,
                        422,
                        'El diseño ya no está disponible para checkout.'
                    );
                }

                if ($item->print_job_id) {
                    $job = PrintJob::query()
                        ->lockForUpdate()
                        ->find($item->print_job_id);

                    abort_unless(
                        $job && ($job->status?->value ?? (string) $job->status) === PrintJobStatus::InCart->value,
                        422,
                        'El trabajo de impresión ya no es válido para checkout.'
                    );
                }

                if ($item->product_variant_id) {
                    $variant = ProductVariant::query()->find($item->product_variant_id);

                    abort_unless(
                        $variant && $variant->is_active,
                        422,
                        'Una variante ya no está disponible.'
                    );
                }

                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_variant_id' => $item->product_variant_id,
                    'product_design_id' => $item->product_design_id,
                    'print_job_id' => $item->print_job_id,
                    'item_name' => $this->resolveItemName($item),
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'subtotal' => $item->subtotal,
                    'metadata' => $item->metadata,
                ]);

                if ($item->product_design_id) {
                    $design = ProductDesign::query()->find($item->product_design_id);

                    if ($design) {
                        $design->status = ProductDesignStatus::Ordered;
                        $design->save();
                    }
                }

                if ($item->print_job_id) {
                    $job = PrintJob::query()->find($item->print_job_id);

                    if ($job) {
                        $job->status = PrintJobStatus::Ordered;
                        $job->save();
                    }
                }
            }

            $cart->status = 'checked_out';
            $cart->save();

            return ApiResponse::success(
                data: $order->load('items'),
                message: 'Pedido creado correctamente.'
            );
        });
    }

    private function resolveItemName(CartItem $item): string
    {
        if ($item->isProductVariant()) {
            return $item->productVariant?->product?->name ?? 'Producto';
        }

        if ($item->isProductDesign()) {
            return 'Diseño personalizado';
        }

        if ($item->isPrintJob()) {
            return 'Trabajo de impresión 3D';
        }

        return 'Item';
    }
}
