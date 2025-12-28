@extends('layouts.admin')

@section('content')
    <div class="max-w-3xl">
        <h1 class="text-2xl font-semibold text-gray-900">
            Editar trabajo de impresión #{{ $printJob->id }}
        </h1>

        <p class="text-sm text-gray-600 mt-1">
            Archivo: <strong>{{ $printFile->original_name }}</strong>
        </p>

        <form method="POST"
              action="{{ route('admin.print-files.jobs.update', [$printFile, $printJob]) }}"
              class="mt-6 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Material
                </label>
                <select name="material_id"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @foreach($materials as $material)
                        <option value="{{ $material->id }}"
                            @selected(old('material_id', $printJob->material_id) == $material->id)>
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
                       value="{{ old('technology', $printJob->technology) }}"
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
                       value="{{ old('color_name', $printJob->color_name) }}"
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
                       value="{{ old('quantity', $printJob->quantity) }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                @error('quantity')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-4">
                <button type="submit"
                        class="px-4 py-2 bg-gray-900 text-green-600 text-sm rounded hover:bg-gray-700">
                    Guardar cambios
                </button>

                <a href="{{ route('admin.print-files.jobs.show', [$printFile, $printJob]) }}"
                   class="text-sm text-gray-600 underline">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection
