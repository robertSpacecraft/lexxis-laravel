@extends('layouts.admin')

@section('content')
    <div class="max-w-3xl">
        <h1 class="text-2xl font-semibold text-gray-900">
            Nuevo trabajo de impresión
        </h1>

        <p class="text-sm text-gray-600 mt-1">
            Archivo: <strong>{{ $printFile->original_name }}</strong>
        </p>

        <form method="POST"
              action="{{ route('admin.print-files.jobs.store', $printFile) }}"
              class="mt-6 space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Material
                </label>
                <select name="material_id"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @foreach($materials as $material)
                        <option value="{{ $material->id }}"
                            @selected(old('material_id') == $material->id)>
                            {{ $material->name }}
                        </option>
                    @endforeach
                </select>
                @error('material_id')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Tecnología
                </label>
                <input type="text"
                       name="technology"
                       value="{{ old('technology', 'fdm') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                @error('technology')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Color
                </label>
                <input type="text"
                       name="color_name"
                       value="{{ old('color_name') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                @error('color_name')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Cantidad
                </label>
                <input type="number"
                       name="quantity"
                       min="1"
                       value="{{ old('quantity', 1) }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                @error('quantity')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Precio unitario (€)
                </label>
                <input type="number"
                       step="0.01"
                       name="unit_price"
                       value="{{ old('unit_price') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                @error('unit_price')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Estado
                </label>
                <select name="status"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @foreach(\App\Enums\PrintJobStatus::cases() as $status)
                        <option value="{{ $status->value }}"
                            @selected(old('status', 'draft') === $status->value)>
                            {{ ucfirst(str_replace('_', ' ', $status->value)) }}
                        </option>
                    @endforeach
                </select>
                @error('status')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-4">
                <button type="submit"
                        class="px-4 py-2 bg-gray-900 text-green-600 text-sm rounded hover:bg-gray-700">
                    Crear job
                </button>

                <a href="{{ route('admin.print-files.jobs.index', $printFile) }}"
                   class="text-sm text-gray-600 underline">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection
