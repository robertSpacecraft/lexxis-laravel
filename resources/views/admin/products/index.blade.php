@extends('layouts.admin')

@section('content')
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900">Productos</h1>

        <a href="{{ route('admin.products.create') }}"
           class="px-4 py-2 bg-blue-700 text-white hover:bg-red-500 rounded-md">
            Nuevo producto
        </a>
    </div>

    @if (session('status'))
        <div class="mt-4 p-3 rounded-md bg-white border text-gray-900">
            {{ session('status') }}
        </div>
    @endif

    <div class="mt-6 bg-white border rounded-lg overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-gray-700">
            <tr>
                <th class="text-left px-4 py-3">Nombre</th>
                <th class="text-left px-4 py-3">Slug</th>
                <th class="text-left px-4 py-3">Activo</th>
                <th class="text-left px-4 py-3">Creado</th>
                <th class="text-left px-4 py-3">Acciones</th>
            </tr>
            </thead>
            <tbody class="divide-y">
            @forelse($products as $product)
                <tr>
                    <td>
                        @if ($product->mainImage)
                        <img
                            src="{{ Storage::url($product->mainImage->path) }}"
                            alt="{{ $product->mainImage->alt_text ?? '' }}"
                            style="height:150px; width:150px; object-fit:contain; border-radius:6px;"
                        >
                    @else
                        <div style="height:40px; width:40px; border:1px solid #ddd; border-radius:6px;"></div>
                    @endif
                    </td>

                    <td class="px-4 py-3 font-medium text-gray-900">{{ $product->name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $product->slug }}</td>
                    <td class="px-4 py-3">
                        @if($product->is_active)
                            <span class="text-green-700">Sí</span>
                        @else
                            <span class="text-gray-500">No</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $product->created_at?->format('Y-m-d H:i') }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-4 text-sm">
                            <a href="{{ route('admin.products.edit', $product) }}"
                               class="underline text-gray-700 hover:text-gray-900">
                                Editar producto
                            </a>

                            <a href="{{ route('admin.products.variants.index', $product) }}"
                               class="underline text-gray-700 hover:text-gray-900">
                                Ver variantes ({{ $product->variants_count }})
                            </a>
                        </div>
                    </td>


                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-gray-600">
                        No hay productos todavía.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection

