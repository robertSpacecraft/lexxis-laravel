@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <div class="text-sm text-gray-600">
            <a class="underline hover:text-gray-900" href="{{ route('admin.users.show', $user) }}">
                Usuario · {{ $user->name }} {{ $user->last_name }}
            </a>
            <span class="mx-2">/</span>
            <a class="underline hover:text-gray-900" href="{{ route('admin.users.orders.index', $user) }}">
                Pedidos
            </a>
            <span class="mx-2">/</span>
            <span class="text-gray-900">{{ $order->order_number }}</span>
        </div>

        <h1 class="text-xl font-semibold text-gray-900 mt-2">
            Pedido · {{ $order->order_number }}
        </h1>
    </div>

    <div class="flex items-center justify-end text-sm">
        <a href="{{ route('admin.orders.edit', $order) }}" class="underline">
            Editar
        </a>
    </div>


    <div class="bg-white shadow rounded p-6 space-y-6">
        <div class="border rounded">
            <div class="bg-gray-50 px-4 py-2 text-sm font-medium text-gray-700">
                Resumen
            </div>

            <div class="p-4 text-sm">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-gray-500">Estado</dt>
                        <dd class="text-gray-900">{{ $order->status->value }}</dd>
                    </div>

                    <div>
                        <dt class="text-gray-500">Pago</dt>
                        <dd class="text-gray-900">{{ $order->payment_status->value }}</dd>
                    </div>

                    <div>
                        <dt class="text-gray-500">Total</dt>
                        <dd class="text-gray-900">{{ number_format((float) $order->total, 2) }} €</dd>
                    </div>

                    <div>
                        <dt class="text-gray-500">Placed at</dt>
                        <dd class="text-gray-900">
                            {{ $order->placed_at ? $order->placed_at->format('Y-m-d H:i') : '—' }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="border rounded">
            <div class="bg-gray-50 px-4 py-2 text-sm font-medium text-gray-700">
                Items
            </div>

            <div class="p-4">
                @if ($order->items->isEmpty())
                    <p class="text-sm text-gray-600">Este pedido no tiene items.</p>
                @else
                    <table class="min-w-full text-sm border">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left">Item</th>
                            <th class="px-4 py-2 text-left">Tipo</th>
                            <th class="px-4 py-2 text-right">Precio</th>
                            <th class="px-4 py-2 text-right">Cantidad</th>
                            <th class="px-4 py-2 text-right">Subtotal</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($order->items as $item)
                            @php
                                $type = $item->product_variant_id ? 'product_variant' : ($item->print_job_id ? 'print_job' : 'unknown');
                            @endphp
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $item->item_name }}</td>
                                <td class="px-4 py-2">{{ $type }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format((float) $item->unit_price, 2) }} €</td>
                                <td class="px-4 py-2 text-right">{{ $item->quantity }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format((float) $item->subtotal, 2) }} €</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        <div class="flex items-center justify-between text-sm">
            <a class="underline text-gray-700 hover:text-gray-900"
               href="{{ route('admin.users.orders.index', $user) }}">
                Volver a pedidos del usuario
            </a>

            <a class="underline text-gray-700 hover:text-gray-900"
               href="{{ route('admin.orders.show', $order) }}">
                Ver en vista global
            </a>
        </div>
    </div>
@endsection
