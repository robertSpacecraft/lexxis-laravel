@extends('layouts.admin')

@section('content')
    <div class="flex items-start justify-between gap-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Editar archivo imprimible</h1>
            <p class="mt-1 text-sm text-gray-600">
                {{ $printFile->original_name }}
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.print-files.show', $printFile) }}"
               class="text-sm text-gray-600 hover:text-gray-900 underline">
                Volver
            </a>

            <a href="{{ route('admin.print-files.download', $printFile) }}"
               class="px-4 py-2 bg-gray-900 text-green-600 text-sm rounded hover:bg-gray-700">
                Descargar
            </a>
        </div>
    </div>

    <div class="mt-6 bg-white border rounded-lg p-6 max-w-2xl">
        <form method="POST" action="{{ route('admin.print-files.update', $printFile) }}">
            @csrf
            @method('PUT')

            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Estado</label>
                <select name="status" class="block w-full text-sm border rounded-md p-2">
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}"
                            @selected(old('status', $printFile->status?->value ?? $printFile->status) === $status->value)>
                            {{ $status->value }}
                        </option>
                    @endforeach
                </select>
                @error('status')
                <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6 space-y-2">
                <label class="block text-sm font-medium text-gray-700">Notas</label>
                <textarea name="notes" rows="4"
                          class="block w-full text-sm border rounded-md p-2">{{ old('notes', $printFile->notes) }}</textarea>
                @error('notes')
                <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6 space-y-2">
                <label class="block text-sm font-medium text-gray-700">Metadata (JSON) - opcional</label>
                <textarea name="metadata" rows="6"
                          class="block w-full text-xs border rounded-md p-2 font-mono"
                          placeholder='ej. {"slicer":"Cura","profile":"TPU"}'>{{ old('metadata', $printFile->metadata ? json_encode($printFile->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '') }}</textarea>
                @error('metadata')
                <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6">
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white hover:bg-red-500 rounded-md text-sm">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
@endsection
