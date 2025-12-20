@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-xl font-semibold">
            Imágenes de variante · {{ $product->name }} · {{ $variant->sku }}
        </h1>


        <a href="{{ route('admin.products.variants.images.create', [$product, $variant]) }}"
           class="px-4 py-2 bg-gray-900 text-green-600 text-sm rounded">
            Nueva imagen
        </a>
        <a href="{{ route('admin.products.variants.edit', [$product, $variant]) }}"
           class="px-4 py-2 bg-gray-100 border rounded-md text-sm hover:bg-gray-200">
            ← Volver a la variante
        </a>
    </div>

    <ul class="bg-white border divide-y">
        @forelse($images as $image)
            <li class="p-3 flex justify-between">
            <li class="p-4 flex gap-6 items-center">
                {{-- Imagen --}}
                <div class="flex-shrink-0">
                    <img
                        src="{{ Storage::url($image->path) }}"
                        alt="{{ $image->alt_text ?? '' }}"
                        style="height:300px; width:220px; object-fit:contain; background:#f5f5f5; border-radius:8px;"
                    >
                </div>

                {{-- Acciones --}}
                <div class="flex flex-col gap-3">
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
                            class="px-4 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700"
                        >
                            Eliminar
                        </button>
                    </form>
                </div>
            </li>


            <form method="POST"
                      action="{{ route('admin.products.variants.images.destroy', [$product, $variant, $image]) }}">
                    @csrf
                    @method('DELETE')
                    <button class="text-red-600 hover:underline" type="submit">
                        Eliminar
                    </button>
                </form>
            </li>
        @empty
            <li class="p-4 text-gray-500">No hay imágenes para esta variante</li>
        @endforelse
    </ul>
@endsection

