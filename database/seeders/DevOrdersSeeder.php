<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PrintJob;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Str;

class DevOrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::query()
            ->whereHas('addresses', function ($q) {
                $q->where('address_type', 'shipping');
            })
            ->take(10)
            ->get();


        if ($users->isEmpty()) {
            $this->command?->warn('No hay usuarios. DevOrdersSeeder no hizo nada.');
            return;
        }

        $variants = ProductVariant::query()->pluck('id')->all();
        $jobs     = PrintJob::query()->pluck('id')->all();

        foreach ($users as $user) {
            //Direcciones del usuario
            $shippingAddresses = Address::query()
                ->where('user_id', $user->id)
                ->where('address_type', 'shipping')
                ->pluck('id')
                ->all();

            $billingAddresses = Address::query()
                ->where('user_id', $user->id)
                ->where('address_type', 'billing')
                ->pluck('id')
                ->all();

            if (empty($shippingAddresses)) {
                $this->command?->warn("User {$user->id} sin direcciones shipping. Se omite.");
                continue;
            }

            $ordersCount = rand(1, 3);

            for ($i = 0; $i < $ordersCount; $i++) {
                $order = Order::factory()->create([
                    'user_id' => $user->id,
                    'shipping_address_id' => $shippingAddresses[array_rand($shippingAddresses)],
                    'billing_address_id' => !empty($billingAddresses)
                        ? $billingAddresses[array_rand($billingAddresses)]
                        : null,
                    'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                ]);

                $itemsCount = rand(2, 5);

                for ($j = 0; $j < $itemsCount; $j++) {
                    $item = OrderItem::factory()->make();

                    $payload = [
                        'order_id' => $order->id,
                        'item_name' => $item->item_name,
                        'unit_price' => $item->unit_price,
                        'quantity' => $item->quantity,
                        'subtotal' => $item->subtotal,
                        'metadata' => $item->metadata,
                        'product_variant_id' => null,
                        'print_job_id' => null,
                    ];

                    //Elegimos tipo de item
                    $canUseVariant = !empty($variants);
                    $canUseJob     = !empty($jobs);

                    if ($canUseVariant && $canUseJob) {
                        // Mezcla: 60% producto / 40% impresión (ajustable)
                        $useVariant = (rand(1, 100) <= 60);
                        if ($useVariant) {
                            $payload['product_variant_id'] = $variants[array_rand($variants)];
                            $payload['item_name'] = $payload['item_name'] ?: 'Product variant';
                        } else {
                            $payload['print_job_id'] = $jobs[array_rand($jobs)];
                            $payload['item_name'] = $payload['item_name'] ?: 'Print job';
                        }
                    } elseif ($canUseVariant) {
                        $payload['product_variant_id'] = $variants[array_rand($variants)];
                        $payload['item_name'] = $payload['item_name'] ?: 'Product variant';
                    } elseif ($canUseJob) {
                        $payload['print_job_id'] = $jobs[array_rand($jobs)];
                        $payload['item_name'] = $payload['item_name'] ?: 'Print job';
                    } else {
                        // No hay variantes ni jobs: evitamos crear items inválidos
                        $this->command?->warn('No hay product_variants ni print_jobs. Se omiten items.');
                        break;
                    }

                    OrderItem::create($payload);
                }

                //Recalcular totales snapshot desde items reales
                $subtotal = OrderItem::query()
                    ->where('order_id', $order->id)
                    ->sum('subtotal');

                $tax = 0;
                $shipping = 0;
                $total = $subtotal + $tax + $shipping;

                $order->update([
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'shipping_cost' => $shipping,
                    'total' => $total,
                ]);
            }
        }
    }
}
