@extends('layouts.admin')

@section('content')
    <h1 class="text-xl font-semibold text-gray-900 mb-6">Nuevo material</h1>

    <form method="POST" action="{{ route('admin.materials.store') }}" class="bg-white p-6 border space-y-4">
        @csrf

        @include('admin.materials.partials.form')

        <div class="flex gap-3">
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-red-500 text-white rounded">
                Guardar
            </button>

            <a href="{{ route('admin.materials.index') }}" class="px-4 py-2 border hover:bg-red-500 rounded">
                Cancelar
            </a>
        </div>
    </form>
@endsection
