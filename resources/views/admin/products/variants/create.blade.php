@extends('layouts.admin')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Nueva variante</h1>
            <p class="mt-1 text-sm text-gray-600">
                Producto: <span class="font-medium text-gray-900">{{ $product->name }}</span>
            </p>
        </div>

        <a href="{{ route('admin.products.variants.index', $product) }}"
           class="text-sm text-gray-600 hover:text-gray-900 underline">
            Volver a variantes
        </a>
    </div>

    <form method="POST"
          action="{{ route('admin.products.variants.store', $product) }}"
          class="space-y-6">
        @csrf

        <div>
            <label for="sku" class="block text-sm font-medium text-gray-700">
                SKU
            </label>
            <input
                type="text"
                id="sku"
                name="sku"
                class="mt-1 block w-full rounded-md border-gray-300"
                required
            >
        </div>

        <div>
            <label for="material_id" class="block text-sm font-medium text-gray-700">
                Material
            </label>
            <select
                id="material_id"
                name="material_id"
                class="mt-1 block w-full rounded-md border-gray-300"
                required
            >
                @foreach($materials as $material)
                    <option value="{{ $material->id }}">
                        {{ $material->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="color_name" class="block text-sm font-medium text-gray-700">
                Color
            </label>
            <input
                type="text"
                id="color_name"
                name="color_name"
                class="mt-1 block w-full rounded-md border-gray-300"
                required
            >
        </div>

        <div>
            <label for="size_eu" class="block text-sm font-medium text-gray-700">
                Talla (EU)
            </label>
            <input
                type="number"
                step="0.5"
                id="size_eu"
                name="size_eu"
                class="mt-1 block w-full rounded-md border-gray-300"
                required
            >
        </div>

        <div>
            <label for="price" class="block text-sm font-medium text-gray-700">
                Precio (€)
            </label>
            <input
                type="number"
                step="0.01"
                id="price"
                name="price"
                class="mt-1 block w-full rounded-md border-gray-300"
                required
            >
        </div>

        <div>
            <label for="stock" class="block text-sm font-medium text-gray-700">
                Stock (opcional)
            </label>
            <input
                type="number"
                id="stock"
                name="stock"
                class="mt-1 block w-full rounded-md border-gray-300"
            >
        </div>

        <div class="flex items-center">
            <input
                type="checkbox"
                id="is_active"
                name="is_active"
                value="1"
                class="rounded border-gray-300"
                checked
            >
            <label for="is_active" class="ml-2 text-sm text-gray-700">
                Variante activa
            </label>
        </div>

        <div class="flex justify-center">
            <button
                type="submit"
                class="px-6 py-2 bg-green-600 text-green-600 rounded-md"
            >
                Crear variante
            </button>
        </div>
    </form>

@endsection
