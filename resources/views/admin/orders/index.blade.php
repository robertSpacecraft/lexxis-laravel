@extends('layouts.admin')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Pedidos</h1>
            <p class="text-sm text-gray-600">Listado global (admin)</p>
        </div>
    </div>

    <div class="bg-white border rounded">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-700">
            <tr>
                <th class="text-left px-4 py-3">Nº pedido</th>
                <th class="text-left px-4 py-3">Usuario</th>
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
                    <td class="px-4 py-3">
                        {{ $order->order_number }}
                    </td>

                    <td class="px-4 py-3">
                        <a class="underline text-gray-700 hover:text-gray-900"
                           href="{{ route('admin.users.show', $order->user) }}">
                            {{ $order->user->name }} {{ $order->user->last_name }}
                        </a>
                        <div class="text-gray-500">{{ $order->user->email }}</div>
                    </td>

                    <td class="px-4 py-3">{{ $order->status }}</td>
                    <td class="px-4 py-3">{{ $order->payment_status }}</td>

                    <td class="px-4 py-3 text-right">
                        {{ number_format((float) $order->total, 2) }} €
                    </td>

                    <td class="px-4 py-3">
                        {{ $order->placed_at ? $order->placed_at->format('Y-m-d H:i') : '—' }}
                    </td>

                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <a class="underline text-gray-700 hover:text-gray-900"
                           href="{{ route('admin.orders.show', $order) }}">
                            Ver
                        </a>

                        <span class="text-gray-300 mx-2">|</span>

                        <a class="underline text-gray-700 hover:text-gray-900"
                           href="{{ route('admin.users.orders.show', [$order->user, $order]) }}">
                            Ver en usuario
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="px-4 py-6 text-gray-600" colspan="7">
                        No hay pedidos.
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
