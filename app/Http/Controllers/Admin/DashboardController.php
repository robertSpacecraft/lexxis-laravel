<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\PrintJobStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PrintFile;
use App\Models\PrintJob;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $pendingReviewStatus = PrintJobStatus::ReviewPending->value;

        $kpis = [
            [
                'label' => 'Usuarios registrados',
                'value' => number_format(User::query()->count(), 0, ',', '.'),
                'hint' => 'Cuentas totales',
                'tone' => 'blue',
            ],
            [
                'label' => 'Pedidos totales',
                'value' => number_format(Order::query()->count(), 0, ',', '.'),
                'hint' => 'Pedidos creados',
                'tone' => 'gray',
            ],
            [
                'label' => 'Ingresos totales',
                'value' => number_format((float) Order::query()
                    ->where('payment_status', PaymentStatus::PAID->value)
                    ->sum('total'), 2, ',', '.') . ' €',
                'hint' => 'Pedidos pagados',
                'tone' => 'green',
            ],
            [
                'label' => 'Archivos 3D subidos',
                'value' => number_format(PrintFile::query()->count(), 0, ',', '.'),
                'hint' => 'Archivos imprimibles',
                'tone' => 'purple',
            ],
            [
                'label' => 'Pendientes de revisión',
                'value' => number_format(PrintJob::query()
                    ->where('status', $pendingReviewStatus)
                    ->count(), 0, ',', '.'),
                'hint' => 'Trabajos de impresión',
                'tone' => 'orange',
            ],
        ];

        $orderStatusSummary = Order::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->orderBy('status')
            ->get()
            ->map(fn ($row) => [
                'status' => $row->status?->value ?? (string) $row->status,
                'label' => $this->orderStatusLabel($row->status?->value ?? (string) $row->status),
                'count' => (int) $row->total,
            ]);

        $bestSellingModels = $this->bestSellingModels();

        $latestOrders = Order::query()
            ->with('user')
            ->orderByDesc('placed_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $pendingPrintJobs = PrintJob::query()
            ->with(['user', 'printFile', 'material'])
            ->where('status', $pendingReviewStatus)
            ->latest('id')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'kpis',
            'orderStatusSummary',
            'bestSellingModels',
            'latestOrders',
            'pendingPrintJobs'
        ));
    }

    private function bestSellingModels()
    {
        $variantSales = DB::table('order_items')
            ->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->whereNotNull('order_items.product_variant_id')
            ->select(
                'products.id',
                'products.name',
                'products.slug',
                DB::raw('SUM(order_items.quantity) as units'),
                DB::raw('SUM(order_items.subtotal) as revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.slug')
            ->get();

        $designSales = DB::table('order_items')
            ->join('product_designs', 'order_items.product_design_id', '=', 'product_designs.id')
            ->join('products', 'product_designs.product_id', '=', 'products.id')
            ->whereNotNull('order_items.product_design_id')
            ->select(
                'products.id',
                'products.name',
                'products.slug',
                DB::raw('SUM(order_items.quantity) as units'),
                DB::raw('SUM(order_items.subtotal) as revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.slug')
            ->get();

        return $variantSales
            ->merge($designSales)
            ->groupBy('id')
            ->map(function ($rows) {
                $first = $rows->first();

                return (object) [
                    'id' => $first->id,
                    'name' => $first->name,
                    'slug' => $first->slug,
                    'units' => (int) $rows->sum(fn ($row) => (int) $row->units),
                    'revenue' => (float) $rows->sum(fn ($row) => (float) $row->revenue),
                ];
            })
            ->sortByDesc('units')
            ->take(5)
            ->values();
    }

    private function orderStatusLabel(string $status): string
    {
        return match ($status) {
            OrderStatus::PENDING->value => 'Pendiente',
            OrderStatus::PROCESSING->value => 'En preparación',
            OrderStatus::COMPLETED->value => 'Completado',
            OrderStatus::CANCELLED->value => 'Cancelado',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }
}
