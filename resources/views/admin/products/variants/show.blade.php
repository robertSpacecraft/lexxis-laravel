@extends('layouts.admin')

@section('content')
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                Variante <span class="font-mono">{{ $variant->sku }}</span>
            </h1>

            <p class="mt-1 text-sm text-gray-600">
                Producto:
                <span class="font-medium text-gray-900">{{ $product->name }}</span>
            </p>
        </div>

        <div class="flex items-center gap-4">
            <a href="{{ route('admin.products.variants.index', $product) }}"
               class="text-sm text-gray-600 hover:text-gray-900 underline">
                Volver a variantes
            </a>

            <a href="{{ route('admin.products.variants.edit', [$product, $variant]) }}"
               class="px-4 py-2 bg-gray-900 text-green-600 rounded-md text-sm">
                Editar variante
            </a>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- DATOS (izquierda) --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white border rounded-lg p-4 text-sm">
                <h2 class="text-sm font-semibold text-gray-700 mb-4">Datos</h2>

                <div class="space-y-3">
                    <div>
                        <div class="text-gray-500">SKU</div>
                        <div class="font-medium font-mono text-gray-900">{{ $variant->sku }}</div>
                    </div>

                    <div>
                        <div class="text-gray-500">Material</div>
                        <div class="font-medium text-gray-900">{{ $variant->material->name ?? '—' }}</div>
                    </div>

                    <div>
                        <div class="text-gray-500">Color</div>
                        <div class="font-medium text-gray-900">{{ $variant->color_name }}</div>
                    </div>

                    <div>
                        <div class="text-gray-500">Talla EU</div>
                        <div class="font-medium text-gray-900">{{ $variant->size_eu }}</div>
                    </div>

                    <div>
                        <div class="text-gray-500">Precio</div>
                        <div class="font-medium text-gray-900">{{ $variant->price }}</div>
                    </div>

                    <div>
                        <div class="text-gray-500">Stock</div>
                        <div class="font-medium text-gray-900">{{ $variant->stock ?? '—' }}</div>
                    </div>

                    <div>
                        <div class="text-gray-500">Activo</div>
                        @if($variant->is_active)
                            <span class="text-green-700 font-medium">Sí</span>
                        @else
                            <span class="text-gray-500 font-medium">No</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ACCIONES (editar + gestionar imágenes) --}}
            <div class="bg-white border rounded-lg p-4 space-y-3">
                <a href="{{ route('admin.products.variants.edit', [$product, $variant]) }}"
                   class="block text-sm text-gray-700 underline">
                    Editar variante
                </a>

                <a href="{{ route('admin.products.variants.images.index', [$product, $variant]) }}"
                   class="block text-sm text-gray-700 underline">
                    Gestionar imágenes de esta variante
                </a>
            </div>
        </div>

        {{-- IMÁGENES (derecha) --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border rounded-lg p-4">
                <h2 class="text-sm font-semibold text-gray-700 mb-3">Imagen principal</h2>

                @if($variant->mainImage)
                    <div class="bg-gray-50 border rounded-lg p-3">
                        <img
                            src="{{ Storage::url($variant->mainImage->path) }}"
                            alt="{{ $variant->mainImage->alt_text ?? '' }}"
                            class="w-full max-h-[210px] object-contain"
                        >
                    </div>
                @else
                    <div class="h-[200px] flex items-center justify-center text-sm text-gray-500 border rounded bg-gray-50">
                        Sin imagen principal
                    </div>
                @endif
            </div>

            <div class="bg-white border rounded-lg p-4">
                <h2 class="text-sm font-semibold text-gray-700 mb-3">Galería</h2>

                @if($variant->images->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($variant->images as $image)
                            <div class="bg-gray-50 border rounded-lg p-2">
                                <img
                                    src="{{ Storage::url($image->path) }}"
                                    alt="{{ $image->alt_text ?? '' }}"
                                    class="h-24 w-full object-contain"
                                >
                                @if($image->is_main)
                                    <div class="mt-2 text-xs text-green-700 font-medium">Principal</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-sm text-gray-500">
                        Esta variante no tiene imágenes.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
