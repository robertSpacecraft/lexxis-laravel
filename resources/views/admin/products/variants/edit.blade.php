@extends('layouts.admin')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Editar variante</h1>
            <p class="mt-1 text-sm text-gray-600">
                Producto: <span class="font-medium text-gray-900">{{ $product->name }}</span>
            </p>
        </div>

        <a href="{{ route('admin.products.variants.index', $product) }}"
           class="text-sm text-gray-600 hover:text-gray-900 underline">
            Volver a variantes
        </a>
    </div>

    <div class="mt-6 bg-white border rounded-lg p-6">
        <form method="POST"
              action="{{ route('admin.products.variants.update', [$product, $variant]) }}"
              class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="material_id" class="block text-sm font-medium text-gray-700">
                    Material
                </label>
                <select id="material_id" name="material_id" class="mt-1 block w-full rounded-md border-gray-300">
                    @foreach($materials as $material)
                        <option value="{{ $material->id }}"
                            @selected($material->id === $variant->material_id)>
                            {{ $material->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="sku" class="block text-sm font-medium text-gray-700">SKU</label>
                <input type="text" id="sku" name="sku"
                       value="{{ old('sku', $variant->sku) }}"
                       class="mt-1 block w-full rounded-md border-gray-300" required>
            </div>

            <div>
                <label for="size_eu" class="block text-sm font-medium text-gray-700">Talla EU</label>
                <input type="number" step="0.5" id="size_eu" name="size_eu"
                       value="{{ old('size_eu', $variant->size_eu) }}"
                       class="mt-1 block w-full rounded-md border-gray-300" required>
            </div>

            <div>
                <label for="color_name" class="block text-sm font-medium text-gray-700">Color</label>
                <input type="text" id="color_name" name="color_name"
                       value="{{ old('color_name', $variant->color_name) }}"
                       class="mt-1 block w-full rounded-md border-gray-300" required>
            </div>

            <div>
                <label for="price" class="block text-sm font-medium text-gray-700">Precio (€)</label>
                <input type="number" step="0.01" id="price" name="price"
                       value="{{ old('price', $variant->price) }}"
                       class="mt-1 block w-full rounded-md border-gray-300" required>
            </div>

            <div>
                <label for="stock" class="block text-sm font-medium text-gray-700">Stock</label>
                <input type="number" id="stock" name="stock"
                       value="{{ old('stock', $variant->stock) }}"
                       class="mt-1 block w-full rounded-md border-gray-300">
            </div>

            <div class="flex items-center">
                <input type="checkbox" id="is_active" name="is_active" value="1"
                       class="rounded border-gray-300"
                    @checked(old('is_active', $variant->is_active))>
                <label for="is_active" class="ml-2 text-sm text-gray-700">Variante activa</label>
            </div>

            <div class="flex justify-center">
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-red-500 text-white rounded-md">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
@endsection

