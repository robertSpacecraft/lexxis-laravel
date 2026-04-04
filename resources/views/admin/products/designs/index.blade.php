@extends('layouts.admin')

@section('content')
    <div class="bg-white shadow-sm rounded-lg p-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between mb-6">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">
                    Diseños de producto · {{ $product->name }}
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    Diseños personalizados creados por usuarios para este producto.
                </p>
            </div>

            <div class="flex flex-wrap gap-3 text-sm">
                <a href="{{ route('admin.products.index') }}" class="underline text-gray-700 hover:text-gray-900">
                    Volver a productos
                </a>
                <a href="{{ route('admin.products.variants.index', $product) }}" class="underline text-gray-700 hover:text-gray-900">
                    Ver variantes
                </a>
                <a href="{{ route('admin.product-designs.index', ['product_id' => $product->id]) }}" class="underline text-gray-700 hover:text-gray-900">
                    Vista global filtrada
                </a>
            </div>
        </div>

        @if($designs->isEmpty())
            <div class="rounded border border-dashed border-gray-300 p-6 text-sm text-gray-600">
                Este producto no tiene diseños personalizados registrados.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                    <tr class="border-b text-left text-gray-600">
                        <th class="py-3 pr-4">ID</th>
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
                            <td class="py-3 pr-4 font-medium text-gray-900">#{{ $design->id }}</td>
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
                                <div class="flex flex-col gap-1">
                                    <a href="{{ route('admin.products.designs.show', [$product, $design]) }}"
                                       class="text-gray-900 underline hover:text-gray-700">
                                        Ver
                                    </a>
                                    <a href="{{ route('admin.products.designs.edit', [$product, $design]) }}"
                                       class="text-gray-900 underline hover:text-gray-700">
                                        Editar
                                    </a>
                                </div>
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
