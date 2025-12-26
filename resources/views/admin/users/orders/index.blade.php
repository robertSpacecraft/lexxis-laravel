@extends('layouts.admin')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Pedidos de usuario</h1>
            <p class="text-sm text-gray-600">
                <a class="underline" href="{{ route('admin.users.show', $user) }}">
                    {{ $user->name }} {{ $user->last_name }}
                </a>
                · {{ $user->email }}
            </p>
        </div>

        <div class="flex items-center gap-3 text-sm">
            <a class="underline text-gray-700 hover:text-gray-900"
               href="{{ route('admin.orders.index') }}">
                Ver listado global
            </a>
        </div>
    </div>

    <div class="bg-white border rounded">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-700">
            <tr>
                <th class="text-left px-4 py-3">Nº pedido</th>
                <th class="text-left px-4 py-3">Estado</th>
                <th class="text-left px-4 py-3">Pago</th>
                <th class="text-right px-4 py-3">Total</th>
                <th class="text-left px-4 py-3">Placed</th>
                <th class="text-right px-4 py-3">Acciones</th>
            </tr>
            </thead>
            <tbody class="divide-y">
            @forelse($orders as $order)
                <tr>
                    <td class="px-4 py-3">{{ $order->order_number }}</td>
                    <td class="px-4 py-3">{{ $order->status->value }}</td>
                    <td class="px-4 py-3">{{ $order->payment_status->value }}</td>
                    <td class="px-4 py-3 text-right">{{ number_format((float) $order->total, 2) }} €</td>
                    <td class="px-4 py-3">
                        {{ $order->placed_at ? $order->placed_at->format('Y-m-d H:i') : '—' }}
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <a class="underline text-gray-700 hover:text-gray-900"
                           href="{{ route('admin.users.orders.show', [$user, $order]) }}">
                            Ver
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="px-4 py-6 text-gray-600" colspan="6">
                        Este usuario no tiene pedidos.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
@endsection
