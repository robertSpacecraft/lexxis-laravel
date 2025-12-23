@extends('layouts.admin')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Subir archivo imprimible</h1>
            <p class="mt-1 text-sm text-gray-600">
                El archivo quedará asociado a tu usuario (admin) y se almacenará de forma privada.
            </p>
        </div>

        <a href="{{ route('admin.print-files.index') }}"
           class="text-sm text-gray-600 hover:text-gray-900 underline">
            Volver
        </a>
    </div>

    <div class="mt-6 bg-white border rounded-lg p-6 max-w-2xl">
        <form method="POST" action="{{ route('admin.print-files.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Archivo</label>
                <input type="file" name="file"
                       class="block w-full text-sm border rounded-md p-2">
                @error('file')
                <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500">
                    Opciones: STL, OBJ, 3MF, STEP, GCODE.
                </p>
            </div>

            <div class="mt-6 space-y-2">
                <label class="block text-sm font-medium text-gray-700">Notas (opcional)</label>
                <textarea name="notes" rows="4"
                          class="block w-full text-sm border rounded-md p-2">{{ old('notes') }}</textarea>
                @error('notes')
                <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6">
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white hover:bg-red-500 rounded-md text-sm">
                    Subir archivo
                </button>
            </div>
        </form>
    </div>
@endsection
