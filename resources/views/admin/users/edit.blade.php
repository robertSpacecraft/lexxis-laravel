@extends('layouts.admin')

@section('content')
    <div class="bg-white shadow rounded p-6 space-y-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-gray-800">
                    Editar usuario · {{ $user->name }} {{ $user->last_name }}
                </h1>
                <p class="text-sm text-gray-600 mt-1">{{ $user->email }}</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.users.show', $user) }}"
                   class="text-sm text-gray-700 hover:text-gray-900 underline">
                    Cancelar
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="border border-red-200 bg-red-50 text-red-700 rounded p-4 text-sm">
                <p class="font-medium mb-2">Hay errores en el formulario:</p>
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input type="text"
                           name="name"
                           value="{{ old('name', $user->name) }}"
                           class="mt-1 w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Apellidos</label>
                    <input type="text"
                           name="last_name"
                           value="{{ old('last_name', $user->last_name) }}"
                           class="mt-1 w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email"
                           name="email"
                           value="{{ old('email', $user->email) }}"
                           class="mt-1 w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                    <input type="text"
                           name="phone"
                           value="{{ old('phone', $user->phone) }}"
                           class="mt-1 w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Rol</label>
                    <select name="role" class="mt-1 w-full border rounded px-3 py-2">
                        @foreach (\App\Enums\UserRole::cases() as $role)
                            <option value="{{ $role->value }}"
                                @selected(old('role', $user->role->value) === $role->value)>
                                {{ $role->value }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Estado
                    </label>

                    <input type="hidden" name="is_active" value="0">

                    <label class="inline-flex items-center gap-2">
                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            @checked(old('is_active', $user->is_active))
                            class="rounded border-gray-300 text-gray-900 focus:ring-gray-900"
                        >
                        <span class="text-sm text-gray-700">Usuario activo</span>
                    </label>
                </div>

            </div>

            <div class="flex items-center gap-3">
                <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-red-500">
                    Guardar cambios
                </button>

                <a href="{{ route('admin.users.show', $user) }}"
                   class="text-sm text-gray-700 hover:text-gray-900 underline">
                    Volver sin guardar
                </a>
            </div>
        </form>
    </div>
@endsection
