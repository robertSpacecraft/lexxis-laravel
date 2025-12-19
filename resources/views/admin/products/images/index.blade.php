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
            <li class="p-3 flex justify-between">
                <span>{{ $image->path }}</span>
                <a href="{{ route('admin.products.images.edit', [$product, $image]) }}"
                   class="text-blue-600">Editar</a>
            </li>
        @empty
            <li class="p-4 text-gray-500">No hay imágenes</li>
        @endforelse
    </ul>
@endsection

