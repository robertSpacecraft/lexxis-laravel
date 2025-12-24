@extends('layouts.admin')

@section('content')
    <div class="bg-white shadow rounded p-6">
        <h1 class="text-xl font-semibold text-gray-800 mb-4">
            Usuarios
        </h1>

        <table class="min-w-full text-sm border">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left">Nombre</th>
                <th class="px-4 py-2 text-left">Email</th>
                <th class="px-4 py-2 text-left">Rol</th>
                <th class="px-4 py-2 text-left">Estado</th>
                <th class="px-4 py-2 text-left">Creado</th>
                <th class="px-4 py-2 text-right">Acciones</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($users as $user)
                <tr class="border-t">
                    <td class="px-4 py-2">
                        {{ $user->name }} {{ $user->last_name }}
                    </td>

                    <td class="px-4 py-2">
                        {{ $user->email }}
                    </td>

                    <td class="px-4 py-2">
                        {{ $user->role->value }}
                    </td>

                    <td class="px-4 py-2">
                        @if ($user->is_active)
                            <span class="text-green-600 font-medium">Activo</span>
                        @else
                            <span class="text-red-600 font-medium">Inactivo</span>
                        @endif
                    </td>

                    <td class="px-4 py-2">
                        {{ $user->created_at->format('d/m/Y') }}
                    </td>

                    <td class="px-4 py-2 text-right space-x-2">
                        <a href="{{ route('admin.users.show', $user) }}"
                           class="text-blue-600 hover:underline">
                            Ver
                        </a>

                        <a href="{{ route('admin.users.edit', $user) }}"
                           class="text-gray-600 hover:underline">
                            Editar
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                        No hay usuarios registrados.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
@endsection

