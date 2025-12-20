@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-xl font-semibold">Imágenes · {{ $product->name }}</h1>

        <a href="{{ route('admin.products.images.create', $product) }}"
           class="px-4 py-2 bg-gray-900 text-green-600 text-sm rounded">
            Nueva imagen
        </a>
    </div>

    <ul class="bg-white border divide-y">
        @forelse($images as $image)
            <li class="p-4 flex gap-6 items-center">
                <div class="flex-shrink-0">
                    <img
                        src="{{ Storage::url($image->path) }}"
                        alt="{{ $image->alt_text ?? '' }}"
                        style="height:300px; width:220px; object-fit:contain; background:#f5f5f5; border-radius:8px;"
                    >
                </div>

                <div class="flex flex-col gap-3">
                    <a href="{{ route('admin.products.images.edit', [$product, $image]) }}"
                       class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm text-center hover:bg-blue-700">
                        Editar
                    </a>

                    <form method="POST"
                          action="{{ route('admin.products.images.destroy', [$product, $image]) }}"
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
            <li class="p-4 text-gray-500">No hay imágenes</li>
        @endforelse
    </ul>
@endsection

