@extends('layouts.admin')

@section('content')
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900">
            Editar producto
        </h1>

        <a href="{{ route('admin.products.index') }}"
           class="text-sm text-gray-600 hover:text-gray-900 underline">
            Volver a productos
        </a>
    </div>

    <div class="mt-6 bg-white border rounded-lg p-6">
        <form method="POST"
              action="{{ route('admin.products.update', $product) }}"
              class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">
                    Nombre comercial
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $product->name) }}"
                    class="mt-1 block w-full rounded-md border-gray-300"
                    required
                >
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">
                    Descripción
                </label>
                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    class="mt-1 block w-full rounded-md border-gray-300"
                >{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="flex items-center">
                <input
                    type="checkbox"
                    id="is_active"
                    name="is_active"
                    value="1"
                    class="rounded border-gray-300"
                    @checked(old('is_active', $product->is_active))
                >
                <label for="is_active" class="ml-2 text-sm text-gray-700">
                    Producto activo
                </label>
            </div>

            <div class="flex justify-center">
                <button
                    type="submit"
                    class="px-4 py-2 bg-blue-600 text-white hover:bg-red-500 border-2 rounded-md font-semibold"
                >
                    Guardar cambios
                </button>
            </div>
        </form>
        <div class="flex justify-end">
            <form method="POST"
                  action="{{ route('admin.products.destroy', $product) }}"
                  onsubmit="return confirm('¿Seguro que quieres eliminar este producto?')">
                @csrf
                @method('DELETE')

                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md">
                    Eliminar producto
                </button>
            </form>
        </div>

    </div>
    <div class="flex items-center justify-between bg-gray-50 border rounded-lg p-4 mb-6">
        <div class="flex items-center gap-4">
            @if ($product->mainImage)
                <img
                    src="{{ Storage::url($product->mainImage->path) }}"
                    alt="{{ $product->mainImage->alt_text ?? '' }}"
                    style="height:80px; width:80px; object-fit:contain; background:#f5f5f5; border-radius:8px;"
                >
            @else
                <div style="height:80px; width:80px; border:1px solid #ddd; border-radius:8px; background:#fff;"></div>
            @endif

            <div>
                <div class="text-sm text-gray-700 font-medium">Imagen principal</div>
                <div class="text-xs text-gray-500">Gestiona y cambia la principal desde Imágenes</div>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.products.images.index', $product) }}"
               class="px-3 py-2 bg-white border rounded-md text-sm hover:bg-gray-100">
                Gestionar imágenes
            </a>

            <a href="{{ route('admin.products.images.create', $product) }}"
               class="px-3 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">
                Subir imagen
            </a>
        </div>
    </div>

@endsection

