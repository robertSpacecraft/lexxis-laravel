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
    </div>

    <ul class="bg-white border divide-y">
        @forelse($images as $image)
            <li class="p-3 flex justify-between">
                <span>{{ $image->path }}</span>

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

