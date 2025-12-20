@extends('layouts.admin')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold">
            Editar imagen · Variante {{ $variant->sku }}
        </h1>

        <a href="{{ route('admin.products.variants.images.index', [$product, $variant]) }}"
           class="text-sm text-gray-600 hover:text-gray-900 underline">
            Volver a imágenes
        </a>
    </div>

    <div class="bg-white border rounded-lg p-6 space-y-6">
        <div>
            <img
                src="{{ Storage::url($image->path) }}"
                alt="{{ $image->alt_text ?? '' }}"
                style="height:320px; width:320px; object-fit:contain; background:#f5f5f5; border-radius:10px;"
            >
        </div>

        <form method="POST"
              action="{{ route('admin.products.variants.images.update', [$product, $variant, $image]) }}"
              enctype="multipart/form-data"
              class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="image" class="block text-sm font-medium text-gray-700">
                    Reemplazar archivo (opcional)
                </label>
                <input
                    id="image"
                    name="image"
                    type="file"
                    accept="image/jpeg,image/png"
                    class="mt-1 block w-full text-sm text-gray-700"
                >
                <p class="mt-1 text-xs text-gray-500">
                    Si subes un archivo nuevo, se sustituirá la imagen actual.
                </p>
            </div>

            <div>
                <label for="alt_text" class="block text-sm font-medium text-gray-700">
                    Texto alternativo (alt)
                </label>
                <input
                    id="alt_text"
                    name="alt_text"
                    type="text"
                    value="{{ old('alt_text', $image->alt_text) }}"
                    class="mt-1 block w-full rounded-md border-gray-300"
                >
            </div>

            <div class="flex items-center">
                <input
                    type="checkbox"
                    id="is_main"
                    name="is_main"
                    value="1"
                    class="rounded border-gray-300"
                    @checked(old('is_main', $image->is_main))
                >
                <label for="is_main" class="ml-2 text-sm text-gray-700">
                    Establecer como principal
                </label>
            </div>

            <div class="flex justify-end">
                <button
                    type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700"
                >
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
@endsection
