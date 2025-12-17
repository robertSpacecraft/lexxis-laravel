@extends('layouts.admin')

@section('content')
    <h1 class="text-xl font-semibold text-gray-900 mb-6">Editar material</h1>

    <form method="POST"
          action="{{ route('admin.materials.update', $material) }}"
          class="bg-white p-6 border space-y-4">
        @csrf
        @method('PUT')

        @include('admin.materials.partials.form', ['material' => $material])

        <div class="flex gap-3">
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-red-500 text-white rounded">
                Guardar cambios
            </button>

            <a href="{{ route('admin.materials.index') }}" class="px-4 py-2 hover:bg-red-500 border rounded">
                Cancelar
            </a>
        </div>
    </form>
@endsection

