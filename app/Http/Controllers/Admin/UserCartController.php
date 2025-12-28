<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Carts\AddPrintJobToCartRequest;
use App\Http\Requests\Carts\AddProductVariantToCartRequest;
use App\Http\Requests\Carts\CheckoutCartRequest;
use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PrintJob;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\PrintJobPricingService;

class UserCartController extends Controller
{
    public function show(User $user)
    {
        // Busco el carrito activo del usuario (y precargo relaciones para renderizar la vista).
        // Uso latest por si más adelante permito histórico de carritos.
        $cart = Cart::query()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->with(['items.productVariant', 'items.printJob'])
            ->latest('id')
            ->first();

        return view('admin.users.cart.show', compact('user', 'cart'));
    }

    // Para añadir un ProductVariant al carrito
    public function addProductVariant(AddProductVariantToCartRequest $request, User $user, ProductVariant $variant)
    {
        $quantityToAdd = (int) $request->validated()['quantity'];

        // Garantizo que existe un carrito activo para este usuario.
        $cart = Cart::query()->firstOrCreate(
            [
                'user_id' => $user->id,
                'status'  => 'active',
            ],
            []
        );

        // Precio unitario del variant (si no existe aún, lo dejo en 0 por seguridad).
        $unitPrice = (string) data_get($variant, 'price', '0.00');

        DB::transaction(function () use ($cart, $variant, $quantityToAdd, $unitPrice) {
            // Bloqueo la posible línea existente para evitar carreras si se envían dos requests a la vez.
            $item = CartItem::query()
                ->where('cart_id', $cart->id)
                ->where('product_variant_id', $variant->id)
                ->whereNull('print_job_id')
                ->lockForUpdate()
                ->first();

            if ($item) {
                $newQty = $item->quantity + $quantityToAdd;

                $item->update([
                    'quantity'   => $newQty,
                    'unit_price' => $unitPrice,
                    'subtotal'   => $this->moneyMul($unitPrice, $newQty),
                ]);

                return;
            }

            CartItem::query()->create([
                'cart_id'            => $cart->id,
                'product_variant_id' => $variant->id,
                'print_job_id'       => null,
                'quantity'           => $quantityToAdd,
                'unit_price'         => $unitPrice,
                'subtotal'           => $this->moneyMul($unitPrice, $quantityToAdd),
                'metadata'           => [
                    'source' => 'product_variant',
                ],
            ]);
        });

        return redirect()
            ->route('admin.users.cart.show', $user)
            ->with('success', 'Producto añadido al carrito.');
    }

    // Para añadir un PrintJob al carrito
    public function addPrintJob(AddPrintJobToCartRequest $request, User $user, PrintJob $printJob)
    {
        //El PrintJob debe pertenecer a ese usuario.
        abort_unless($printJob->user_id === $user->id, 404);

        //El estado actual (enum o string)
        $status = $printJob->status?->value ?? (string) $printJob->status;

        //Solo se permiten estos estados para esta operación así lo protejo mejor
        abort_unless(in_array($status, ['draft', 'priced', 'in_cart'], true), 422);

        //Me aseguro y garantizo que existe un carrito activo.
        $cart = Cart::query()->firstOrCreate(
            [
                'user_id' => $user->id,
                'status'  => 'active',
            ],
            []
        );

        $quantityToAdd = $request->validatedQuantity();

        DB::transaction(function () use ($cart, $printJob, $quantityToAdd, &$status) {

            //Bloqueo el PrintJob
            $printJob->lockForUpdate();

            /*
             * Si está en draft, aquí calculo el precio,
             * se persiste el snapshot y se pasa a priced.
             */
            if ($status === 'draft') {
                $pricingService = app(PrintJobPricingService::class);

                $quote = $pricingService->quote($printJob);

                $printJob->update([
                    'estimated_material_g' => $quote['estimated_material_g'],
                    'estimated_time_min'   => $quote['estimated_time_min'],
                    'unit_price'           => $quote['unit_price'],
                    'pricing_breakdown'    => $quote['pricing_breakdown'],
                    'status'               => 'priced',
                ]);

                $status = 'priced';
            }

            //priced/in_cart siempre debe tener precio
            abort_unless(!is_null($printJob->unit_price), 422, 'El PrintJob no tiene unit_price.');

            //Si entra al carrito por primera vez, reflejo el estado
            if ($status === 'priced') {
                $printJob->status = 'in_cart';
                $printJob->save();
            }

            $unitPrice = (string) $printJob->unit_price;

            //Busco si ya existe línea para este PrintJob
            $existing = CartItem::query()
                ->where('cart_id', $cart->id)
                ->where('print_job_id', $printJob->id)
                ->whereNull('product_variant_id')
                ->lockForUpdate()
                ->first();

            if ($existing) {
                $newQty = $existing->quantity + $quantityToAdd;

                $existing->update([
                    'quantity'   => $newQty,
                    'unit_price' => $unitPrice,
                    'subtotal'   => $this->moneyMul($unitPrice, $newQty),
                ]);

                return;
            }

            CartItem::query()->create([
                'cart_id'            => $cart->id,
                'print_job_id'       => $printJob->id,
                'product_variant_id' => null,
                'quantity'           => $quantityToAdd,
                'unit_price'         => $unitPrice,
                'subtotal'           => $this->moneyMul($unitPrice, $quantityToAdd),
                'metadata'           => [
                    'source' => 'print_job',
                ],
            ]);
        });

        return redirect()
            ->route('admin.users.cart.show', $user)
            ->with('success', 'PrintJob añadido al carrito.');
    }


    // Método para convertir un Cart en un Order
    public function checkout(CheckoutCartRequest $request, User $user)
    {
        // Valido direcciones primero (si vienen en request).
        $shippingAddressId = $request->shippingAddressId();
        $billingAddressId  = $request->billingAddressId();

        if ($shippingAddressId !== null) {
            $shipping = Address::query()->findOrFail($shippingAddressId);
            abort_unless($shipping->user_id === $user->id, 404);
        }

        if ($billingAddressId !== null) {
            $billing = Address::query()->findOrFail($billingAddressId);
            abort_unless($billing->user_id === $user->id, 404);
        }

        $paymentMethod = $request->paymentMethod();
        $notes = $request->notes();

        return DB::transaction(function () use (
            $user,
            $shippingAddressId,
            $billingAddressId,
            $paymentMethod,
            $notes
        ) {
            // En checkout bloqueo el carrito activo para evitar que cambie mientras genero el pedido.
            $cart = Cart::query()
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->lockForUpdate()
                ->first();

            abort_if(!$cart, 404, 'El usuario no tiene carrito activo.');

            // Cargo los items ya dentro de la transacción y con lock.
            $items = CartItem::query()
                ->where('cart_id', $cart->id)
                ->lockForUpdate()
                ->get();

            abort_if($items->isEmpty(), 422, 'El carrito está vacío.');

            // Cálculos básicos (de momento sin IVA ni envío).
            $subtotal = (float) $items->sum(fn (CartItem $i) => (float) $i->subtotal);
            $tax = 0.00;
            $shippingCost = 0.00;
            $total = $subtotal + $tax + $shippingCost;

            // Si no se especifican direcciones, tiro de una “por defecto”.
            $shippingId = $shippingAddressId ?? $this->defaultUserAddressId($user, 'shipping');
            $billingId  = $billingAddressId ?? $this->defaultUserAddressId($user, 'billing');

            $order = Order::query()->create([
                'user_id'             => $user->id,
                'shipping_address_id' => $shippingId,
                'billing_address_id'  => $billingId,
                'order_number'        => $this->generateOrderNumber(),
                'status'              => 'pending',
                'payment_status'      => 'pending',
                'payment_method'      => $paymentMethod,
                'subtotal'            => $this->money($subtotal),
                'tax'                 => $this->money($tax),
                'shipping_cost'       => $this->money($shippingCost),
                'total'               => $this->money($total),
                'placed_at'           => now(),
                'notes'               => $notes,
            ]);

            // Convierto cada CartItem en OrderItem.
            foreach ($items as $item) {
                $isProduct = !is_null($item->product_variant_id);
                $isPrint   = !is_null($item->print_job_id);

                // Integridad: una línea es o producto o printjob, pero no ambos.
                abort_unless($isProduct xor $isPrint, 500);

                $itemName = $isProduct
                    ? ('Variant #' . $item->product_variant_id)
                    : ('PrintJob #' . $item->print_job_id);

                OrderItem::query()->create([
                    'order_id'           => $order->id,
                    'product_variant_id' => $item->product_variant_id,
                    'print_job_id'       => $item->print_job_id,
                    'item_name'          => $itemName,
                    'unit_price'         => $item->unit_price,
                    'quantity'           => $item->quantity,
                    'subtotal'           => $item->subtotal,
                    'metadata'           => $item->metadata,
                ]);

                // Si es PrintJob, lo marco como ordered para mantener coherencia con el ciclo.
                if ($isPrint) {
                    $printJob = PrintJob::query()->whereKey($item->print_job_id)->lockForUpdate()->first();
                    if ($printJob) {
                        $status = $printJob->status?->value ?? (string) $printJob->status;
                        if (in_array($status, ['draft', 'priced', 'in_cart'], true)) {
                            $printJob->status = 'ordered';
                            $printJob->save();
                        }
                    }
                }
            }

            // Cierro el carrito.
            $cart->status = 'ordered';
            $cart->save();

            return redirect()
                ->route('admin.orders.show', $order)
                ->with('success', 'Carrito convertido en pedido.');
        });
    }

    private function moneyMul(string|float $unitPrice, int $quantity): string
    {
        $value = (float) $unitPrice * $quantity;
        return number_format(round($value, 2), 2, '.', '');
    }

    private function generateOrderNumber(): string
    {
        return 'ORD-' . Str::upper(Str::random(10));
    }

    private function money(float $value): string
    {
        return number_format(round($value, 2), 2, '.', '');
    }

    private function defaultUserAddressId(User $user, string $type): int
    {
        // Si tienes address_type en la tabla, lo uso para coger una por defecto coherente.
        // Si no existiera (o aún no lo estás usando), puedes simplificar a ->first().
        $address = $user->addresses()
            ->where('address_type', $type)
            ->first();

        if (!$address) {
            $address = $user->addresses()->first();
        }

        abort_unless($address, 422, 'El usuario no tiene direcciones registradas.');
        return (int) $address->id;
    }
}
