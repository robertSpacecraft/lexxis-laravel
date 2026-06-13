<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PrintJob;
use App\Models\ProductDesign;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Seeder;

class DevOrdersSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::query()
            ->whereIn('email', [
                'demo@lexxis.test',
                'maria@lexxis.test',
                'carlos@lexxis.test',
            ])
            ->get()
            ->keyBy('email');

        if ($users->isEmpty()) {
            $this->command?->warn('No hay usuarios demo. DevOrdersSeeder no hizo nada.');
            return;
        }

        foreach ($this->demoOrders() as $payload) {
            $user = $users->get($payload['email']);

            if (!$user) {
                $this->command?->warn("Se omite pedido {$payload['order_number']} por faltar usuario {$payload['email']}.");
                continue;
            }

            $shippingAddress = $this->addressFor($user, 'shipping');
            $billingAddress = $this->addressFor($user, 'billing') ?? $shippingAddress;

            if (!$shippingAddress) {
                $this->command?->warn("Se omite pedido {$payload['order_number']} porque el usuario no tiene dirección.");
                continue;
            }

            $order = Order::updateOrCreate(
                ['order_number' => $payload['order_number']],
                [
                    'user_id' => $user->id,
                    'shipping_address_id' => $shippingAddress->id,
                    'billing_address_id' => $billingAddress?->id,
                    'status' => $payload['status'],
                    'payment_status' => $payload['payment_status'],
                    'payment_method' => $payload['payment_method'],
                    'subtotal' => 0,
                    'tax' => 0,
                    'shipping_cost' => 0,
                    'total' => 0,
                    'placed_at' => now()->subDays($payload['days_ago']),
                    'notes' => $payload['notes'],
                ]
            );

            $this->seedItems($order, $payload['items']);
            $this->recalculateTotals($order);
        }
    }

    private function demoOrders(): array
    {
        return [
            [
                'email' => 'demo@lexxis.test',
                'order_number' => 'ORD-DEMO-0001',
                'status' => OrderStatus::PENDING,
                'payment_status' => PaymentStatus::PENDING,
                'payment_method' => 'card',
                'days_ago' => 1,
                'notes' => 'Pedido demo pendiente para revisión funcional.',
                'items' => [
                    ['kind' => 'variant', 'sku' => 'FLOW-BLK-42-95A', 'quantity' => 1],
                    ['kind' => 'print_job', 'file' => 'demo_suela_future.stl', 'quantity' => 1],
                ],
            ],
            [
                'email' => 'maria@lexxis.test',
                'order_number' => 'ORD-DEMO-0002',
                'status' => OrderStatus::PROCESSING,
                'payment_status' => PaymentStatus::PAID,
                'payment_method' => 'transfer',
                'days_ago' => 4,
                'notes' => 'Pedido demo en preparación.',
                'items' => [
                    ['kind' => 'variant', 'sku' => 'URBAN-WHT-43-95A', 'quantity' => 1],
                    ['kind' => 'design', 'email' => 'maria@lexxis.test', 'color_name' => 'Azul', 'quantity' => 1],
                ],
            ],
            [
                'email' => 'carlos@lexxis.test',
                'order_number' => 'ORD-DEMO-0003',
                'status' => OrderStatus::COMPLETED,
                'payment_status' => PaymentStatus::PAID,
                'payment_method' => 'card',
                'days_ago' => 12,
                'notes' => 'Pedido demo completado.',
                'items' => [
                    ['kind' => 'variant', 'sku' => 'XPORT-BLU-43-95A', 'quantity' => 1],
                ],
            ],
        ];
    }

    private function addressFor(User $user, string $type): ?Address
    {
        return Address::query()
            ->where('user_id', $user->id)
            ->where('address_type', $type)
            ->orderBy('id')
            ->first();
    }

    private function seedItems(Order $order, array $items): void
    {
        foreach ($items as $item) {
            match ($item['kind']) {
                'variant' => $this->seedVariantItem($order, $item),
                'design' => $this->seedDesignItem($order, $item),
                'print_job' => $this->seedPrintJobItem($order, $item),
                default => null,
            };
        }
    }

    private function seedVariantItem(Order $order, array $item): void
    {
        $variant = ProductVariant::query()
            ->where('sku', $item['sku'])
            ->with('product')
            ->first();

        if (!$variant) {
            $this->command?->warn("Se omite item de pedido: no existe variante {$item['sku']}.");
            return;
        }

        $quantity = (int) $item['quantity'];
        $unitPrice = (string) ($variant->price ?? $variant->product?->base_price ?? '0.00');

        OrderItem::updateOrCreate(
            [
                'order_id' => $order->id,
                'product_variant_id' => $variant->id,
                'product_design_id' => null,
                'print_job_id' => null,
            ],
            [
                'item_name' => $variant->product?->name ?? 'Producto de catálogo',
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $this->moneyMul($unitPrice, $quantity),
                'metadata' => [
                    'source' => 'demo_seed',
                    'sku' => $variant->sku,
                    'color_name' => $variant->color_name,
                    'size_eu' => $variant->size_eu,
                ],
            ]
        );
    }

    private function seedDesignItem(Order $order, array $item): void
    {
        $user = User::query()->where('email', $item['email'])->first();

        if (!$user) {
            return;
        }

        $design = ProductDesign::query()
            ->where('user_id', $user->id)
            ->where('color_name', $item['color_name'])
            ->with('product')
            ->first();

        if (!$design) {
            $this->command?->warn("Se omite item de pedido: no existe diseño {$item['color_name']}.");
            return;
        }

        $quantity = (int) $item['quantity'];
        $unitPrice = (string) ($design->unit_price ?? '0.00');

        OrderItem::updateOrCreate(
            [
                'order_id' => $order->id,
                'product_variant_id' => null,
                'product_design_id' => $design->id,
                'print_job_id' => null,
            ],
            [
                'item_name' => $design->product?->name
                    ? 'Diseño: ' . $design->product->name
                    : 'Diseño personalizado',
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $this->moneyMul($unitPrice, $quantity),
                'metadata' => [
                    'source' => 'demo_seed',
                    'color_name' => $design->color_name,
                    'size_eu' => $design->size_eu,
                ],
            ]
        );
    }

    private function seedPrintJobItem(Order $order, array $item): void
    {
        $job = PrintJob::query()
            ->whereHas('printFile', fn ($query) => $query->where('original_name', $item['file']))
            ->with('printFile')
            ->first();

        if (!$job) {
            $this->command?->warn("Se omite item de pedido: no existe print job para {$item['file']}.");
            return;
        }

        $quantity = (int) $item['quantity'];
        $unitPrice = (string) ($job->unit_price ?? '0.00');

        OrderItem::updateOrCreate(
            [
                'order_id' => $order->id,
                'product_variant_id' => null,
                'product_design_id' => null,
                'print_job_id' => $job->id,
            ],
            [
                'item_name' => 'Impresión: ' . ($job->printFile?->original_name ?? 'archivo 3D'),
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $this->moneyMul($unitPrice, $quantity),
                'metadata' => [
                    'source' => 'demo_seed',
                    'technology' => $job->technology,
                    'color_name' => $job->color_name,
                ],
            ]
        );
    }

    private function recalculateTotals(Order $order): void
    {
        $subtotal = (float) OrderItem::query()
            ->where('order_id', $order->id)
            ->sum('subtotal');

        $order->update([
            'subtotal' => number_format($subtotal, 2, '.', ''),
            'tax' => '0.00',
            'shipping_cost' => '0.00',
            'total' => number_format($subtotal, 2, '.', ''),
        ]);
    }

    private function moneyMul(string|float $unitPrice, int $quantity): string
    {
        return number_format(round((float) $unitPrice * $quantity, 2), 2, '.', '');
    }
}
