@extends('layouts.admin')

@section('content')
    <div class="bg-white shadow-sm rounded-lg p-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Diseños personalizados</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Vista global de los ProductDesign creados por los usuarios.
                </p>
            </div>
        </div>

        <form method="GET" class="grid gap-4 md:grid-cols-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                <input
                    type="text"
                    name="q"
                    value="{{ $filters['q'] ?? '' }}"
                    class="w-full rounded border-gray-300"
                    placeholder="Producto, usuario, material, color..."
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="status" class="w-full rounded border-gray-300">
                    <option value="">Todos</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>
                            {{ $status->value }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Producto</label>
                <select name="product_id" class="w-full rounded border-gray-300">
                    <option value="">Todos</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" @selected((string)($filters['product_id'] ?? '') === (string)$product->id)>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Usuario</label>
                <select name="user_id" class="w-full rounded border-gray-300">
                    <option value="">Todos</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected((string)($filters['user_id'] ?? '') === (string)$user->id)>
                            {{ $user->name }} {{ $user->last_name }} · {{ $user->email }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-4 flex items-center gap-3">
                <button type="submit" class="inline-flex items-center rounded bg-gray-900 px-4 py-2 text-white text-sm hover:bg-gray-800">
                    Filtrar
                </button>

                <a href="{{ route('admin.product-designs.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                    Limpiar filtros
                </a>
            </div>
        </form>

        @if($designs->isEmpty())
            <div class="rounded border border-dashed border-gray-300 p-6 text-sm text-gray-600">
                No hay diseños que coincidan con los filtros aplicados.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                    <tr class="border-b text-left text-gray-600">
                        <th class="py-3 pr-4">ID</th>
                        <th class="py-3 pr-4">Producto</th>
                        <th class="py-3 pr-4">Usuario</th>
                        <th class="py-3 pr-4">Material</th>
                        <th class="py-3 pr-4">Color</th>
                        <th class="py-3 pr-4">Talla</th>
                        <th class="py-3 pr-4">Precio</th>
                        <th class="py-3 pr-4">Estado</th>
                        <th class="py-3 pr-4">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($designs as $design)
                        <tr class="border-b align-top">
                            <td class="py-3 pr-4 text-gray-900 font-medium">#{{ $design->id }}</td>
                            <td class="py-3 pr-4">
                                <div class="font-medium text-gray-900">{{ $design->product?->name }}</div>
                                @if($design->product)
                                    <a href="{{ route('admin.products.designs.index', $design->product) }}"
                                       class="text-xs text-gray-600 underline hover:text-gray-900">
                                        Ver diseños de este producto
                                    </a>
                                @endif
                            </td>
                            <td class="py-3 pr-4">
                                @if($design->user)
                                    <a href="{{ route('admin.users.show', $design->user) }}"
                                       class="text-gray-900 underline hover:text-gray-700">
                                        {{ $design->user->name }} {{ $design->user->last_name }}
                                    </a>
                                    <div class="text-xs text-gray-600">{{ $design->user->email }}</div>
                                @endif
                            </td>
                            <td class="py-3 pr-4">{{ $design->material?->name ?? '—' }}</td>
                            <td class="py-3 pr-4">{{ $design->color_name ?? '—' }}</td>
                            <td class="py-3 pr-4">{{ $design->size_eu ?? '—' }}</td>
                            <td class="py-3 pr-4">{{ $design->unit_price !== null ? number_format((float)$design->unit_price, 2, ',', '.') . ' €' : '—' }}</td>
                            <td class="py-3 pr-4">
                                <span class="inline-flex rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-700">
                                    {{ $design->status?->value ?? $design->status }}
                                </span>
                            </td>
                            <td class="py-3 pr-4">
                                @if($design->product)
                                    <div class="flex flex-col gap-1">
                                        <a href="{{ route('admin.products.designs.show', [$design->product, $design]) }}"
                                           class="text-gray-900 underline hover:text-gray-700">
                                            Ver
                                        </a>
                                        <a href="{{ route('admin.products.designs.edit', [$design->product, $design]) }}"
                                           class="text-gray-900 underline hover:text-gray-700">
                                            Editar
                                        </a>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $designs->links() }}
            </div>
        @endif
    </div>
@endsection
