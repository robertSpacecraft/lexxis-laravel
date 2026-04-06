@extends('layouts.admin')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold">
                Nueva imagen de variante · {{ $product->name }} · {{ $variant->sku }}
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                Sube una imagen asociada a esta variante.
            </p>
        </div>

        <a href="{{ route('admin.products.variants.images.index', [$product, $variant]) }}"
           class="text-sm text-gray-600 hover:text-gray-900 underline">
            Volver a imágenes
        </a>
    </div>

    @if (session('status'))
        <div class="mb-4 p-3 rounded-md bg-white border text-gray-900">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-md border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800">
            <div class="font-semibold mb-2">Error al subir la imagen</div>
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ route('admin.products.variants.images.store', [$product, $variant]) }}"
          enctype="multipart/form-data"
          class="bg-white p-6 border rounded-lg space-y-6">
        @csrf

        <div>
            <label for="image" class="block text-sm font-medium text-gray-700 mb-1">
                Archivo
            </label>
            <input
                id="image"
                type="file"
                name="image"
                accept="image/jpeg,image/png,image/webp"
                required
                class="block w-full text-sm text-gray-700"
            >
            <p class="mt-1 text-xs text-gray-500">
                Formatos permitidos: JPG, JPEG, PNG, WEBP. Tamaño máximo: 4 MB.
            </p>
            @error('image')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="alt_text" class="block text-sm font-medium text-gray-700 mb-1">
                Texto alternativo
            </label>
            <input
                id="alt_text"
                type="text"
                name="alt_text"
                value="{{ old('alt_text') }}"
                class="w-full rounded-md border-gray-300"
            >
            @error('alt_text')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center">
            <input
                id="is_main"
                type="checkbox"
                name="is_main"
                value="1"
                class="rounded border-gray-300"
                @checked(old('is_main'))
            >
            <label for="is_main" class="ml-2 text-sm text-gray-700">
                Marcar como imagen principal
            </label>
            @error('is_main')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <button
                type="submit"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md"
            >
                Subir imagen
            </button>
        </div>
    </form>
@endsection
