@extends('layouts.admin')

@section('content')
    <div class="mb-8 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Dashboard admin</h1>
            <p class="mt-1 text-sm text-gray-600">Resumen operativo de usuarios, pedidos y fabricación.</p>
        </div>

        <a href="{{ route('admin.orders.index') }}"
           class="inline-flex items-center justify-center rounded border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 hover:border-gray-500">
            Ver pedidos
        </a>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        @foreach($kpis as $kpi)
            <div class="rounded-lg border bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm font-medium text-gray-600">{{ $kpi['label'] }}</p>
                    <span @class([
                        'h-2.5 w-2.5 rounded-full',
                        'bg-blue-400' => $kpi['tone'] === 'blue',
                        'bg-gray-400' => $kpi['tone'] === 'gray',
                        'bg-green-400' => $kpi['tone'] === 'green',
                        'bg-purple-400' => $kpi['tone'] === 'purple',
                        'bg-orange-400' => $kpi['tone'] === 'orange',
                    ])></span>
                </div>
                <div class="mt-4 text-2xl font-semibold text-gray-900">{{ $kpi['value'] }}</div>
                <div class="mt-1 text-xs text-gray-500">{{ $kpi['hint'] }}</div>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <section class="rounded-lg border bg-white p-6 shadow-sm lg:col-span-1">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Pedidos por estado</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-600 underline">Ver todos</a>
            </div>

            @php
                $maxStatusCount = max((int) $orderStatusSummary->max('count'), 1);
            @endphp

            <div class="mt-5 space-y-4">
                @forelse($orderStatusSummary as $row)
                    <div>
                        <div class="mb-1 flex items-center justify-between text-sm">
                            <span class="font-medium text-gray-700">{{ $row['label'] }}</span>
                            <span class="text-gray-500">{{ $row['count'] }}</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                            <div class="h-2 rounded-full bg-gray-700"
                                 style="width: {{ max(6, round(($row['count'] / $maxStatusCount) * 100)) }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-600">No hay pedidos registrados.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-lg border bg-white p-6 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Modelos más vendidos</h2>
                <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-600 underline">Ver productos</a>
            </div>

            @php
                $maxUnits = max((int) $bestSellingModels->max('units'), 1);
            @endphp

            <div class="mt-5 space-y-4">
                @forelse($bestSellingModels as $model)
                    <div>
                        <div class="mb-1 flex items-center justify-between gap-4 text-sm">
                            <div>
                                <div class="font-medium text-gray-800">{{ $model->name }}</div>
                                <div class="text-xs text-gray-500">{{ $model->slug }}</div>
                            </div>
                            <div class="text-right text-gray-600">
                                <div>{{ $model->units }} uds.</div>
                                <div class="text-xs">{{ number_format($model->revenue, 2, ',', '.') }} €</div>
                            </div>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                            <div class="h-2 rounded-full bg-green-500"
                                 style="width: {{ max(6, round(($model->units / $maxUnits) * 100)) }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-600">Todavía no hay ventas asociadas a modelos del catálogo.</p>
                @endforelse
            </div>
        </section>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <section class="overflow-hidden rounded-lg border bg-white shadow-sm">
            <div class="flex items-center justify-between border-b px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">Últimos pedidos</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-600 underline">Abrir listado</a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left">Pedido</th>
                        <th class="px-4 py-3 text-left">Cliente</th>
                        <th class="px-4 py-3 text-left">Estado</th>
                        <th class="px-4 py-3 text-right">Total</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y">
                    @forelse($latestOrders as $order)
                        <tr>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="font-medium text-gray-800 underline">
                                    {{ $order->order_number }}
                                </a>
                                <div class="text-xs text-gray-500">
                                    {{ $order->placed_at ? $order->placed_at->format('d/m/Y H:i') : 'Sin fecha' }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-700">
                                {{ $order->user?->name ?? '—' }} {{ $order->user?->last_name ?? '' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded bg-gray-100 px-2 py-1 text-xs text-gray-700">
                                    {{ $order->status?->value ?? $order->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right text-gray-800">
                                {{ number_format((float) $order->total, 2, ',', '.') }} €
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-gray-600">No hay pedidos recientes.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="overflow-hidden rounded-lg border bg-white shadow-sm">
            <div class="flex items-center justify-between border-b px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">Trabajos pendientes de revisión</h2>
                <a href="{{ route('admin.print-jobs.review-pending') }}" class="text-sm text-gray-600 underline">Revisar</a>
            </div>

            <div class="divide-y">
                @forelse($pendingPrintJobs as $job)
                    <div class="px-6 py-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                @if($job->printFile)
                                    <a href="{{ route('admin.print-files.jobs.show', [$job->printFile, $job]) }}"
                                       class="font-medium text-gray-800 underline">
                                        #{{ $job->id }} · {{ $job->printFile->original_name }}
                                    </a>
                                @else
                                    <div class="font-medium text-gray-800">
                                        #{{ $job->id }} · Archivo sin nombre
                                    </div>
                                @endif
                                <div class="mt-1 text-sm text-gray-600">
                                    {{ $job->user?->name ?? '—' }} {{ $job->user?->last_name ?? '' }}
                                    · {{ $job->material?->name ?? 'Material no definido' }}
                                </div>
                            </div>
                            <div class="whitespace-nowrap text-right text-sm text-gray-700">
                                @if(is_null($job->unit_price))
                                    Pendiente
                                @else
                                    {{ number_format((float) $job->unit_price, 2, ',', '.') }} €
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-6 text-sm text-gray-600">No hay trabajos pendientes de revisión.</div>
                @endforelse
            </div>
        </section>
    </div>

    <section class="mt-6 rounded-lg border bg-white p-6 shadow-sm">
        <div class="mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Accesos rápidos</h2>
            <p class="mt-1 text-sm text-gray-600">Módulos principales del panel.</p>
        </div>

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <a href="{{ route('admin.products.index') }}" class="rounded border px-4 py-3 text-sm text-gray-700 hover:border-gray-500">Productos</a>
            <a href="{{ route('admin.product-designs.index') }}" class="rounded border px-4 py-3 text-sm text-gray-700 hover:border-gray-500">Diseños personalizados</a>
            <a href="{{ route('admin.orders.index') }}" class="rounded border px-4 py-3 text-sm text-gray-700 hover:border-gray-500">Pedidos</a>
            <a href="{{ route('admin.users.index') }}" class="rounded border px-4 py-3 text-sm text-gray-700 hover:border-gray-500">Usuarios</a>
            <a href="{{ route('admin.materials.index') }}" class="rounded border px-4 py-3 text-sm text-gray-700 hover:border-gray-500">Materiales</a>
            <a href="{{ route('admin.print-files.index') }}" class="rounded border px-4 py-3 text-sm text-gray-700 hover:border-gray-500">Archivos imprimibles</a>
            <a href="{{ route('admin.print-jobs.review-pending') }}" class="rounded border px-4 py-3 text-sm text-gray-700 hover:border-gray-500">Pendientes de revisión</a>
        </div>
    </section>
@endsection
