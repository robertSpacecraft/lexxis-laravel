@extends('layouts.admin')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">
                Pedido {{ $order->order_number }}
            </h1>
            <p class="text-sm text-gray-600">
                Usuario:
                <a class="underline text-gray-700 hover:text-gray-900"
                   href="{{ route('admin.users.show', $order->user) }}">
                    {{ $order->user->name }} {{ $order->user->last_name }}
                </a>
                · {{ $order->user->email }}
            </p>
        </div>

        <a href="{{ route('admin.orders.index') }}"
           class="text-sm text-gray-600 hover:text-gray-900 underline">
            Volver
        </a>
        <a href="{{ route('admin.orders.edit', $order) }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
            Editar
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white border rounded p-4 text-sm">
            <h2 class="font-medium text-gray-900 mb-3">Estado</h2>
            <div>Pedido: <span class="text-gray-900">{{ $order->status->value }}</span></div>
            <div>Pago: <span class="text-gray-900">{{ $order->payment_status->value }}</span></div>
            <div>Método: <span class="text-gray-900">{{ $order->payment_method ?? '—' }}</span></div>
        </div>

        <div class="bg-white border rounded p-4 text-sm">
            <h2 class="font-medium text-gray-900 mb-3">Totales (snapshot)</h2>
            <div>Subtotal: <span class="text-gray-900">{{ number_format((float) $order->subtotal, 2) }} €</span></div>
            <div>Impuestos: <span class="text-gray-900">{{ number_format((float) $order->tax, 2) }} €</span></div>
            <div>Envío: <span class="text-gray-900">{{ number_format((float) $order->shipping_cost, 2) }} €</span></div>
            <div class="pt-2 border-t mt-2">
                Total: <span class="text-gray-900 font-semibold">{{ number_format((float) $order->total, 2) }} €</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white border rounded p-4 text-sm">
            <h2 class="font-medium text-gray-900 mb-3">Dirección de envío</h2>
            @php($a = $order->shippingAddress)
            @if($a)
                <div class="text-gray-900">
                    {{ $a->street->name }} {{ $a->street_number }}
                    @if($a->floor), {{ $a->floor }}@endif
                    @if($a->door) {{ $a->door }}@endif
                </div>
                <div class="text-gray-600">
                    {{ $a->street->city->name }},
                    {{ $a->street->city->province->name }},
                    {{ $a->street->city->province->country->name }}
                </div>
                <div class="text-gray-600 mt-2">Contacto: {{ $a->contact_phone ?? '—' }}</div>
            @else
                <div class="text-gray-600">—</div>
            @endif
        </div>

        <div class="bg-white border rounded p-4 text-sm">
            <h2 class="font-medium text-gray-900 mb-3">Dirección de facturación</h2>
            @php($b = $order->billingAddress)
            @if($b)
                <div class="text-gray-900">
                    {{ $b->street->name }} {{ $b->street_number }}
                    @if($b->floor), {{ $b->floor }}@endif
                    @if($b->door) {{ $b->door }}@endif
                </div>
                <div class="text-gray-600">
                    {{ $b->street->city->name }},
                    {{ $b->street->city->province->name }},
                    {{ $b->street->city->province->country->name }}
                </div>
                <div class="text-gray-600 mt-2">Contacto: {{ $b->contact_phone ?? '—' }}</div>
            @else
                <div class="text-gray-600">—</div>
            @endif
        </div>
    </div>

    <div class="bg-white border rounded p-4">
        <h2 class="font-medium text-gray-900 mb-4">Líneas del pedido</h2>

        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-700">
            <tr>
                <th class="text-left px-3 py-2">Concepto</th>
                <th class="text-left px-3 py-2">Tipo</th>
                <th class="text-left px-3 py-2">Referencia</th>
                <th class="text-right px-3 py-2">Cantidad</th>
                <th class="text-right px-3 py-2">Unit</th>
                <th class="text-right px-3 py-2">Subtotal</th>
            </tr>
            </thead>

            <tbody class="divide-y">
            @forelse($order->items as $item)
                <tr>
                    <td class="px-3 py-2">{{ $item->item_name }}</td>

                    <td class="px-3 py-2">
                        @if($item->product_variant_id && !$item->print_job_id)
                            Producto
                        @elseif(!$item->product_variant_id && $item->print_job_id)
                            Servicio impresión
                        @else
                            Inválido (XOR)
                        @endif
                    </td>

                    <td class="px-3 py-2">
                        @if($item->product_variant_id && !$item->print_job_id)
                            <a class="underline text-gray-700 hover:text-gray-900"
                               href="{{ route('admin.products.variants.show', [optional($item->productVariant)->product_id, $item->productVariant]) }}">
                                Variant #{{ $item->product_variant_id }}
                            </a>
                        @elseif(!$item->product_variant_id && $item->print_job_id)
                            <a class="underline text-gray-700 hover:text-gray-900"
                               href="{{ route('admin.print-files.jobs.show', [optional($item->printJob)->print_file_id, $item->printJob]) }}">
                                PrintJob #{{ $item->print_job_id }}
                            </a>
                        @else
                            —
                        @endif
                    </td>

                    <td class="px-3 py-2 text-right">{{ $item->quantity }}</td>
                    <td class="px-3 py-2 text-right">{{ number_format((float) $item->unit_price, 2) }} €</td>
                    <td class="px-3 py-2 text-right">{{ number_format((float) $item->subtotal, 2) }} €</td>
                </tr>
            @empty
                <tr>
                    <td class="px-3 py-6 text-gray-600" colspan="6">Este pedido no tiene líneas.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
