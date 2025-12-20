@extends('layouts.admin')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Variantes</h1>
            <p class="mt-1 text-sm text-gray-600">
                Producto: <span class="font-medium text-gray-900">{{ $product->name }}</span>
            </p>
        </div>

        <div class="flex items-center gap-4">
            <a href="{{ route('admin.products.index', $product) }}"
               class="text-sm text-gray-600 hover:text-gray-900 underline">
                Volver al productos
            </a>

            <a href="{{ route('admin.products.variants.create', $product) }}"
               class="px-4 py-2 bg-gray-900 text-green-600 rounded-md text-sm">
                Nueva variante
            </a>
        </div>
    </div>


    <div class="mt-6 bg-white border rounded-lg overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-gray-700">
            <tr>
                <th class="text-left px-4 py-3">Imagen</th>
                <th class="text-left px-4 py-3">SKU</th>
                <th class="text-left px-4 py-3">Material</th>
                <th class="text-left px-4 py-3">Color</th>
                <th class="text-left px-4 py-3">Talla EU</th>
                <th class="text-left px-4 py-3">Precio</th>
                <th class="text-left px-4 py-3">Stock</th>
                <th class="text-left px-4 py-3">Activo</th>
                <th class="text-right px-4 py-3">Acciones</th>
            </tr>
            </thead>
            <tbody class="divide-y">
            @forelse($variants as $variant)
                <tr>
                    <td class="px-4 py-2">
                        @php
                            $img = $variant->mainImage ?? $product->mainImage;
                        @endphp

                        @if ($img)
                            <img
                                src="{{ Storage::url($img->path) }}"
                                alt="{{ $img->alt_text ?? '' }}"
                                style="height:150px; width:150px; object-fit:contain; background:#f5f5f5; border-radius:6px;"
                            >
                        @else
                            <div style="height:60px; width:60px; border:1px solid #ddd; border-radius:6px; background:#fff;"></div>
                        @endif
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $variant->sku }}</td>
                    <td class="px-4 py-3 text-gray-600">
                        {{ $variant->material->name ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $variant->color_name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $variant->size_eu }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $variant->price }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $variant->stock ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @if($variant->is_active)
                            <span class="text-green-700">Sí</span>
                        @else
                            <span class="text-gray-500">No</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="inline-flex items-center gap-3">
                            <a class="text-sm text-gray-700 underline"
                               href="{{ route('admin.products.variants.edit', [$product, $variant]) }}">
                                Editar
                            </a>

                            <form method="POST"
                                  action="{{ route('admin.products.variants.destroy', [$product, $variant]) }}"
                                  onsubmit="return confirm('¿Seguro que quieres eliminar esta variante?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 underline">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-gray-600">
                        Este producto aún no tiene variantes.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

@endsection

