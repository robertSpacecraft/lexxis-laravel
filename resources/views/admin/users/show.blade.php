@extends('layouts.admin')

@section('content')
    @if (session('success'))
        <div class="mb-4 border border-green-200 bg-green-50 text-green-700 rounded p-4 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow rounded p-6 space-y-6">

        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-gray-800">
                    Usuario · {{ $user->name }} {{ $user->last_name }}
                </h1>

                <p class="text-sm text-gray-600 mt-1">
                    {{ $user->email }}
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.users.edit', $user) }}"
                   class="text-sm text-gray-700 hover:text-gray-900 underline">
                    Editar
                </a>

                <a href="{{ route('admin.users.index') }}"
                   class="text-sm text-gray-700 hover:text-gray-900 underline">
                    Volver
                </a>
            </div>
        </div>

        <div class="border rounded">
            <div class="bg-gray-50 px-4 py-2 text-sm font-medium text-gray-700">
                Datos del usuario
            </div>

            <div class="p-4 text-sm">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-gray-500">Nombre</dt>
                        <dd class="text-gray-900">{{ $user->name }}</dd>
                    </div>

                    <div>
                        <dt class="text-gray-500">Apellidos</dt>
                        <dd class="text-gray-900">{{ $user->last_name }}</dd>
                    </div>

                    <div>
                        <dt class="text-gray-500">Email</dt>
                        <dd class="text-gray-900">{{ $user->email }}</dd>
                    </div>

                    <div>
                        <dt class="text-gray-500">Teléfono</dt>
                        <dd class="text-gray-900">
                            {{ $user->phone ?? '—' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-gray-500">Rol</dt>
                        <dd class="text-gray-900">{{ $user->role->value }}</dd>
                    </div>

                    <div>
                        <dt class="text-gray-500">Estado</dt>
                        <dd class="text-gray-900">
                            @if ($user->is_active)
                                <span class="text-green-600 font-medium">Activo</span>
                            @else
                                <span class="text-red-600 font-medium">Inactivo</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="border rounded">
            <div class="bg-gray-50 px-4 py-2 text-sm font-medium text-gray-700 flex items-center justify-between">
                <span>Direcciones</span>
                {{-- En la siguiente fase irá aquí "Añadir dirección" --}}

                <a href="{{ route('admin.users.addresses.index', $user) }}"
                   class="text-sm text-gray-600 hover:text-gray-900 underline">
                    Ver direcciones
                </a>

            </div>

            <div class="p-4">
                @if ($user->addresses->isEmpty())
                    <p class="text-sm text-gray-600">
                        Este usuario no tiene direcciones registradas.
                    </p>
                @else
                    <table class="min-w-full text-sm border">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left">Alias</th>
                            <th class="px-4 py-2 text-left">Tipo</th>
                            <th class="px-4 py-2 text-left">Dirección</th>
                            <th class="px-4 py-2 text-left">Contacto</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($user->addresses as $address)
                            <tr class="border-t">
                                <td class="px-4 py-2">
                                    {{ $address->alias ?? '—' }}
                                </td>

                                <td class="px-4 py-2">
                                    {{ $address->address_type }}
                                </td>

                                <td class="px-4 py-2">
                                    {{-- Calle --}}
                                    <div class="text-gray-900">
                                        {{ $address->street?->name ?? 'Calle (sin nombre)' }}
                                        {{ $address->street_number }}
                                    </div>

                                    {{-- Piso / puerta (opcionales) --}}
                                    <div class="text-gray-600">
                                        @php
                                            $extras = [];
                                            if ($address->floor) $extras[] = 'Piso ' . $address->floor;
                                            if ($address->door) $extras[] = 'Puerta ' . $address->door;
                                        @endphp

                                        {{ count($extras) ? implode(' · ', $extras) : '—' }}
                                    </div>
                                </td>

                                <td class="px-4 py-2">
                                    {{ $address->contact_phone ?? ($user->phone ?? '—') }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <p class="text-xs text-gray-500 mt-3">
                        Nota: esta sección es solo lectura por ahora.
                    </p>
                @endif
            </div>
        </div>
        <div class="border rounded">
            <div class="bg-gray-50 px-4 py-2 text-sm font-medium text-gray-700 flex items-center justify-between">
                <span>Pedidos</span>

                <a href="{{ route('admin.users.orders.index', $user) }}"
                   class="text-sm text-gray-600 hover:text-gray-900 underline">
                    Ver pedidos ({{ $ordersCount }})
                </a>
            </div>

            <div class="p-4">
                @if ($recentOrders->isEmpty())
                    <p class="text-sm text-gray-600">
                        Este usuario no tiene pedidos.
                    </p>
                @else
                    <table class="min-w-full text-sm border">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left">Nº pedido</th>
                            <th class="px-4 py-2 text-left">Estado</th>
                            <th class="px-4 py-2 text-left">Pago</th>
                            <th class="px-4 py-2 text-right">Total</th>
                            <th class="px-4 py-2 text-left">Fecha</th>
                            <th class="px-4 py-2 text-right">Acción</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($recentOrders as $order)
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $order->order_number }}</td>
                                <td class="px-4 py-2">{{ $order->status->value }}</td>
                                <td class="px-4 py-2">{{ $order->payment_status->value }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format((float) $order->total, 2) }} €</td>
                                <td class="px-4 py-2">
                                    {{ $order->placed_at ? $order->placed_at->format('Y-m-d H:i') : '—' }}
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <a class="underline text-gray-700 hover:text-gray-900"
                                       href="{{ route('admin.orders.show', $order) }}">
                                        Ver
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <p class="text-xs text-gray-500 mt-3">
                        Mostrando los últimos {{ $recentOrders->count() }} pedidos.
                    </p>
                @endif
            </div>
        </div>

    </div>
@endsection
