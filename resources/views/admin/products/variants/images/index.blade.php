@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-xl font-semibold">
                Imágenes de variante · {{ $product->name }} · {{ $variant->sku }}
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                Gestiona las imágenes asociadas a esta variante.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.products.variants.edit', [$product, $variant]) }}"
               class="px-4 py-2 bg-gray-100 border rounded-md text-sm hover:bg-gray-200">
                ← Volver a la variante
            </a>

            <a href="{{ route('admin.products.variants.images.create', [$product, $variant]) }}"
               class="px-4 py-2 bg-gray-900 text-white text-sm rounded hover:bg-gray-800">
                Nueva imagen
            </a>
        </div>
    </div>

    @if (session('status'))
        <div class="mb-4 p-3 rounded-md bg-white border text-gray-900">
            {{ session('status') }}
        </div>
    @endif

    <ul class="bg-white border rounded-lg divide-y">
        @forelse($images as $image)
            <li class="p-4 flex gap-6 items-center justify-between">
                <div class="flex gap-6 items-center">
                    <div class="flex-shrink-0">
                        <img
                            src="{{ Storage::url($image->path) }}"
                            alt="{{ $image->alt_text ?? '' }}"
                            style="height:300px; width:220px; object-fit:contain; background:#f5f5f5; border-radius:8px;"
                        >
                    </div>

                    <div class="space-y-2 text-sm">
                        <div>
                            <span class="font-medium text-gray-700">ID:</span>
                            <span class="text-gray-600">{{ $image->id }}</span>
                        </div>

                        <div>
                            <span class="font-medium text-gray-700">Ruta:</span>
                            <span class="text-gray-600 break-all">{{ $image->path }}</span>
                        </div>

                        <div>
                            <span class="font-medium text-gray-700">Alt:</span>
                            <span class="text-gray-600">{{ $image->alt_text ?: '—' }}</span>
                        </div>

                        <div>
                            <span class="font-medium text-gray-700">Orden:</span>
                            <span class="text-gray-600">{{ $image->sort_order }}</span>
                        </div>

                        <div>
                            <span class="font-medium text-gray-700">Principal:</span>
                            @if($image->is_main)
                                <span class="text-green-700 font-medium">Sí</span>
                            @else
                                <span class="text-gray-500">No</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-3 min-w-[140px]">
                    <a href="{{ route('admin.products.variants.images.edit', [$product, $variant, $image]) }}"
                       class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm text-center hover:bg-blue-700">
                        Editar
                    </a>

                    <form method="POST"
                          action="{{ route('admin.products.variants.images.destroy', [$product, $variant, $image]) }}"
                          onsubmit="return confirm('¿Seguro que quieres eliminar esta imagen?');">
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-md text-sm text-center hover:bg-red-700 w-full"
                        >
                            Eliminar
                        </button>
                    </form>
                </div>
            </li>
        @empty
            <li class="p-4 text-gray-500">
                No hay imágenes para esta variante.
            </li>
        @endforelse
    </ul>
@endsection
