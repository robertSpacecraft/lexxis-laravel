@extends('layouts.admin')

@section('content')
    <div class="max-w-3xl">
        <h1 class="text-2xl font-semibold text-gray-900">
            Editar trabajo de impresión #{{ $printJob->id }}
        </h1>

        <p class="text-sm text-gray-600 mt-1">
            Archivo: <strong>{{ $printFile->original_name }}</strong>
        </p>

        @php
            $reviewReasons = data_get($printJob->pricing_breakdown, 'review_reasons', []);
        @endphp

        @if($printJob->status->value === 'review_pending' && !empty($reviewReasons))
            <div class="mt-4 p-4 rounded-md bg-orange-50 border border-orange-200">
                <h2 class="text-sm font-semibold text-orange-800">Motivos de revisión manual</h2>
                <ul class="mt-2 list-disc pl-5 text-sm text-orange-800 space-y-1">
                    @foreach($reviewReasons as $reason)
                        <li>{{ $reason }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

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

            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Relleno (%)
                </label>
                <select name="infill_percent"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @foreach([5, 15, 40] as $infill)
                        <option value="{{ $infill }}"
                            @selected((int) old('infill_percent', $printJob->infill_percent) === $infill)>
                            {{ $infill }}%
                        </option>
                    @endforeach
                </select>
                @error('infill_percent')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Escala (%)
                </label>
                <input type="number"
                       name="scale_percent"
                       min="10"
                       max="300"
                       value="{{ old('scale_percent', $printJob->scale_percent) }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                @error('scale_percent')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-4">
                <button type="submit"
                        class="px-4 py-2 bg-gray-900 text-green-600 text-sm rounded hover:bg-gray-700">
                    Guardar cambios
                </button>

                @if($printJob->status->value === 'review_pending')
                    <form method="POST"
                          action="{{ route('admin.print-files.jobs.approve-review', [$printFile, $printJob]) }}"
                          onsubmit="return confirm('¿Aprobar la revisión manual de este trabajo?')"
                          class="inline">
                        @csrf
                        <button type="submit"
                                class="px-4 py-2 bg-green-700 text-white text-sm rounded hover:bg-green-800">
                            Aprobar revisión
                        </button>
                    </form>
                @endif

                <a href="{{ route('admin.print-files.jobs.show', [$printFile, $printJob]) }}"
                   class="text-sm text-gray-600 underline">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection
