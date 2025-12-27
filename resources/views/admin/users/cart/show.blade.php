@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <div class="text-sm text-gray-600">
            <a class="underline hover:text-gray-900" href="{{ route('admin.users.show', $user) }}">
                Usuario · {{ $user->name }} {{ $user->last_name }}
            </a>
            <span class="mx-2">/</span>
            <span class="text-gray-900">Carrito</span>
        </div>

        <h1 class="text-xl font-semibold text-gray-900 mt-2">
            Carrito del usuario
        </h1>
    </div>

    @if(!$cart)
        <div class="bg-white border rounded p-6 text-sm text-gray-600">
            Este usuario no tiene un carrito activo.
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white border rounded p-4 text-sm">
                <h2 class="font-medium text-gray-900 mb-3">Estado</h2>
                <div>Estado: <span class="text-gray-900">{{ $cart->status }}</span></div>
                <div>Items: <span class="text-gray-900">{{ $cart->items->count() }}</span></div>
            </div>

            <div class="bg-white border rounded p-4 text-sm">
                <h2 class="font-medium text-gray-900 mb-3">Totales (carrito)</h2>
                @php($total = $cart->items->sum(fn($i) => (float) $i->subtotal))
                <div class="pt-2">
                    Total: <span class="text-gray-900 font-semibold">{{ number_format($total, 2) }} €</span>
                </div>
            </div>
        </div>

        <div class="bg-white border rounded p-4">
            <h2 class="font-medium text-gray-900 mb-4">Líneas del carrito</h2>

            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-700">
                <tr>
                    <th class="text-left px-3 py-2">Tipo</th>
                    <th class="text-left px-3 py-2">Referencia</th>
                    <th class="text-right px-3 py-2">Cantidad</th>
                    <th class="text-right px-3 py-2">Unit</th>
                    <th class="text-right px-3 py-2">Subtotal</th>
                </tr>
                </thead>

                <tbody class="divide-y">
                @forelse($cart->items as $item)
                    <tr>
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
                        <td class="px-3 py-6 text-gray-600" colspan="5">Este carrito no tiene líneas.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    @endif
@endsection
