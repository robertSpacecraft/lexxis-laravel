@extends('layouts.admin')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold text-gray-900">Materiales</h1>

        <a href="{{ route('admin.materials.create') }}"
           class="px-4 py-2 bg-gray-900 text-green-600 text-sm rounded hover:bg-gray-700">
            Nuevo material
        </a>
    </div>

    <table class="min-w-full bg-white border rounded">
        <thead class="bg-gray-50 text-sm text-gray-600">
        <tr>
            <th class="px-4 py-2 text-left">Nombre</th>
            <th class="px-4 py-2 text-left">Tipo</th>
            <th class="px-4 py-2 text-left">Dureza</th>
            <th class="px-4 py-2 text-left">Estado</th>
            <th class="px-4 py-2"></th>
        </tr>
        </thead>
        <tbody class="text-sm">
        @foreach($materials as $material)
            <tr class="border-t">
                <td class="px-4 py-2">{{ $material->name }}</td>
                <td class="px-4 py-2">{{ $material->material_type }}</td>
                <td class="px-4 py-2">
                    @if($material->shore_scale && $material->shore_value !== null)
                        Shore {{ $material->shore_scale }} {{ $material->shore_value }}
                    @else
                        —
                    @endif
                </td>
                <td class="px-4 py-2">
                    {{ $material->is_active ? 'Activo' : 'Inactivo' }}
                </td>
                <td class="px-4 py-2 text-right">
                    <a href="{{ route('admin.materials.edit', $material) }}"
                       class="text-blue-500 hover:underline">
                        Editar
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="mt-6">
        {{ $materials->links() }}
    </div>
@endsection

