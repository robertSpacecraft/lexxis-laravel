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
    </div>
@endsection

