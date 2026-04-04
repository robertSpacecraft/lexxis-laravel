@extends('layouts.admin')

@section('content')
    <div class="bg-white shadow-sm rounded-lg p-6 max-w-3xl">
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-gray-900">
                Editar diseño #{{ $design->id }} · {{ $product->name }}
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                Ajusta material, color, talla, precio y estado del diseño.
            </p>
        </div>

        <form method="POST" action="{{ route('admin.products.designs.update', [$product, $design]) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <label for="material_id" class="block text-sm font-medium text-gray-700 mb-1">Material</label>
                    <select id="material_id" name="material_id" class="w-full rounded border-gray-300">
                        @foreach($materials as $material)
                            <option value="{{ $material->id }}" @selected((int) old('material_id', $design->material_id) === (int) $material->id)>
                                {{ $material->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('material_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select id="status" name="status" class="w-full rounded border-gray-300">
                        @foreach($statuses as $status)
                            <option value="{{ $status->value }}" @selected(old('status', $design->status?->value ?? $design->status) === $status->value)>
                                {{ $status->value }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="color_name" class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                    <input
                        id="color_name"
                        name="color_name"
                        type="text"
                        value="{{ old('color_name', $design->color_name) }}"
                        class="w-full rounded border-gray-300"
                    >
                    @error('color_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="size_eu" class="block text-sm font-medium text-gray-700 mb-1">Talla EU</label>
                    <input
                        id="size_eu"
                        name="size_eu"
                        type="number"
                        step="0.5"
                        min="1"
                        value="{{ old('size_eu', $design->size_eu) }}"
                        class="w-full rounded border-gray-300"
                    >
                    @error('size_eu')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="unit_price" class="block text-sm font-medium text-gray-700 mb-1">Precio unitario</label>
                    <input
                        id="unit_price"
                        name="unit_price"
                        type="number"
                        step="0.01"
                        min="0"
                        value="{{ old('unit_price', $design->unit_price) }}"
                        class="w-full rounded border-gray-300"
                    >
                    @error('unit_price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="rounded border p-4 bg-gray-50">
                    <div class="text-sm text-gray-500">Usuario creador</div>
                    @if($design->user)
                        <a href="{{ route('admin.users.show', $design->user) }}" class="text-sm font-medium text-gray-900 underline hover:text-gray-700">
                            {{ $design->user->name }} {{ $design->user->last_name }}
                        </a>
                        <div class="text-xs text-gray-600 mt-1">{{ $design->user->email }}</div>
                    @else
                        <div class="text-sm text-gray-900">—</div>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-4">
                <button type="submit"
                        class="inline-flex items-center rounded bg-gray-900 px-4 py-2 text-white text-sm hover:bg-gray-800">
                    Guardar cambios
                </button>

                <a href="{{ route('admin.products.designs.show', [$product, $design]) }}"
                   class="text-sm text-gray-600 underline hover:text-gray-900">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection
