@extends('layouts.admin')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Direcciones</h1>
            <p class="text-sm text-gray-600">
                Usuario: {{ $user->name }} {{ $user->last_name }} · {{ $user->email }}
            </p>
        </div>

        <a href="{{ route('admin.users.show', $user) }}"
           class="text-sm text-gray-600 hover:text-gray-900 underline">
            Volver al usuario
        </a>
    </div>

    <div class="bg-white border rounded">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-700">
            <tr>
                <th class="text-left px-4 py-3">Alias</th>
                <th class="text-left px-4 py-3">Tipo</th>
                <th class="text-left px-4 py-3">Dirección</th>
                <th class="text-left px-4 py-3">Contacto</th>
                <th class="text-right px-4 py-3">Acciones</th>
            </tr>
            </thead>
            <tbody class="divide-y">
            @forelse($addresses as $address)
                <tr>
                    <td class="px-4 py-3">
                        {{ $address->alias ?? '—' }}
                    </td>

                    <td class="px-4 py-3">
                        {{ $address->address_type }}
                    </td>

                    <td class="px-4 py-3">
                        {{ $address->street->name }} {{ $address->street_number }}
                        @if($address->floor) , {{ $address->floor }} @endif
                        @if($address->door)  {{ $address->door }} @endif
                        <div class="text-gray-500">
                            {{ $address->street->city->name }},
                            {{ $address->street->city->province->name }},
                            {{ $address->street->city->province->country->name }}
                        </div>
                    </td>

                    <td class="px-4 py-3">
                        {{ $address->contact_phone ?? '—' }}
                    </td>

                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <a class="text-gray-600 hover:text-gray-900 underline"
                           href="{{ route('admin.users.addresses.show', [$user, $address]) }}">
                            Ver
                        </a>

                        <span class="text-gray-300 mx-2">|</span>

                        <a class="text-gray-600 hover:text-gray-900 underline"
                           href="{{ route('admin.users.addresses.edit', [$user, $address]) }}">
                            Editar
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="px-4 py-6 text-gray-600" colspan="5">
                        Este usuario no tiene direcciones.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
