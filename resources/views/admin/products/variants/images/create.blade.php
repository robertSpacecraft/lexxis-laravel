@extends('layouts.admin')

@section('content')
    <h1 class="text-xl font-semibold mb-6">
        Nueva imagen de variante · {{ $product->name }} · {{ $variant->sku }}
    </h1>

    <form method="POST"
          action="{{ route('admin.products.variants.images.store', [$product, $variant]) }}"
          enctype="multipart/form-data"
          class="bg-white p-6 border space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium">Archivo</label>
            <input type="file" name="image" required>
        </div>

        <div>
            <label class="block text-sm font-medium">Texto alternativo</label>
            <input type="text" name="alt_text" class="w-full border p-2">
        </div>

        <div>
            <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="is_main" value="1">
                Imagen principal
            </label>
        </div>

        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-red-500 text-white rounded">
            Subir imagen
        </button>
    </form>
@endsection
