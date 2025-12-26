@extends('layouts.admin')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Dirección</h1>
            <p class="text-sm text-gray-600">
                Usuario: {{ $user->name }} {{ $user->last_name }} · {{ $user->email }}
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.addresses.index', $user) }}"
               class="text-sm text-gray-600 hover:text-gray-900 underline">
                Volver
            </a>

            <a href="{{ route('admin.users.addresses.edit', [$user, $address]) }}"
               class="text-sm text-gray-600 hover:text-gray-900 underline">
                Editar
            </a>

            <form method="POST"
                  action="{{ route('admin.users.addresses.destroy', [$user, $address]) }}"
                  class="inline"
                  onsubmit="return confirm('¿Eliminar esta dirección? Esta acción no se puede deshacer.')">
                @csrf
                @method('DELETE')

                <button type="submit"
                        class="text-sm text-red-600 hover:text-red-800 underline">
                    Eliminar
                </button>
            </form>

        </div>
    </div>

    <div class="bg-white border rounded p-6 space-y-4 text-sm">
        <div>
            <div class="text-gray-500">Alias</div>
            <div class="text-gray-900">{{ $address->alias ?? '—' }}</div>
        </div>

        <div>
            <div class="text-gray-500">Tipo</div>
            <div class="text-gray-900">{{ $address->address_type }}</div>
        </div>

        <div>
            <div class="text-gray-500">Dirección</div>
            <div class="text-gray-900">
                {{ $address->street->name }} {{ $address->street_number }}
                @if($address->floor) , {{ $address->floor }} @endif
                @if($address->door)  {{ $address->door }} @endif
            </div>
            <div class="text-gray-600">
                {{ $address->street->city->name }},
                {{ $address->street->city->province->name }},
                {{ $address->street->city->province->country->name }}
            </div>
        </div>

        <div>
            <div class="text-gray-500">Teléfono de contacto</div>
            <div class="text-gray-900">{{ $address->contact_phone ?? '—' }}</div>
        </div>

        <div class="pt-4 border-t">
            <div class="text-gray-500">Creada</div>
            <div class="text-gray-900">{{ $address->created_at }}</div>
        </div>
    </div>
@endsection
