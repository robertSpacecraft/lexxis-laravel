@extends('layouts.admin')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Editar dirección</h1>
            <p class="text-sm text-gray-600">
                Usuario: {{ $user->name }} {{ $user->last_name }} · {{ $user->email }}
            </p>
        </div>

        <a href="{{ route('admin.users.addresses.show', [$user, $address]) }}"
           class="text-sm text-gray-600 hover:text-gray-900 underline">
            Cancelar
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 p-4 rounded text-sm">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.users.addresses.update', [$user, $address]) }}" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700">Alias</label>
            <input type="text" name="alias"
                   value="{{ old('alias', $address->alias) }}"
                   class="mt-1 w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Tipo</label>
            <select name="address_type" class="mt-1 w-full border rounded px-3 py-2">
                <option value="shipping" @selected(old('address_type', $address->address_type) === 'shipping')>Shipping</option>
                <option value="billing" @selected(old('address_type', $address->address_type) === 'billing')>Billing</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Calle</label>
            <select name="street_id" class="mt-1 w-full border rounded px-3 py-2">
                @foreach($streets as $street)
                    <option value="{{ $street->id }}"
                        @selected((int) old('street_id', $address->street_id) === $street->id)>
                        {{ $street->name }} — {{ $street->city->name }} ({{ $street->city->province->name }})
                    </option>
                @endforeach
            </select>
            <p class="text-xs text-gray-500 mt-1">
                Lista limitada (DEV). Más adelante se sustituirá por un buscador.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Número</label>
                <input type="text" name="street_number"
                       value="{{ old('street_number', $address->street_number) }}"
                       class="mt-1 w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Piso</label>
                <input type="text" name="floor"
                       value="{{ old('floor', $address->floor) }}"
                       class="mt-1 w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Puerta</label>
                <input type="text" name="door"
                       value="{{ old('door', $address->door) }}"
                       class="mt-1 w-full border rounded px-3 py-2">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Teléfono de contacto</label>
            <input type="text" name="contact_phone"
                   value="{{ old('contact_phone', $address->contact_phone) }}"
                   class="mt-1 w-full border rounded px-3 py-2">
        </div>

        <div class="pt-4">
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-red-500 text-white rounded">
                Guardar cambios
            </button>
        </div>
    </form>
@endsection
