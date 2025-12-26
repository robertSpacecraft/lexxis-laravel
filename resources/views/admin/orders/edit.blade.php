@extends('layouts.admin')

@section('content')
    @if (session('success'))
        <div class="mb-4 border border-green-200 bg-green-50 text-green-700 rounded p-4 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 border border-red-200 bg-red-50 text-red-700 rounded p-4 text-sm">
            <div class="font-medium">Hay errores en el formulario:</div>
            <ul class="list-disc ml-5 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-800">
                Editar pedido · {{ $order->order_number }}
            </h1>
            <p class="text-sm text-gray-600 mt-1">
                Usuario #{{ $order->user_id }} · Total {{ number_format((float) $order->total, 2) }} €
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.orders.show', $order) }}"
               class="text-sm text-gray-700 hover:text-gray-900 underline">
                Volver
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="bg-white shadow rounded p-6 space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Estado</label>
                <select name="status" class="mt-1 w-full border rounded px-3 py-2">
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" @selected(old('status', $order->status->value) === $status->value)>
                            {{ $status->value }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Estado de pago</label>
                <select name="payment_status" class="mt-1 w-full border rounded px-3 py-2">
                    @foreach ($paymentStatuses as $ps)
                        <option value="{{ $ps->value }}" @selected(old('payment_status', $order->payment_status->value) === $ps->value)>
                            {{ $ps->value }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Método de pago</label>
                <input type="text"
                       name="payment_method"
                       value="{{ old('payment_method', $order->payment_method) }}"
                       class="mt-1 w-full border rounded px-3 py-2"
                       placeholder="Ej: card, transfer, paypal…">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Notas internas</label>
                <textarea name="notes"
                          class="mt-1 w-full border rounded px-3 py-2"
                          rows="4"
                          placeholder="Notas solo para administración...">{{ old('notes', $order->notes) }}</textarea>
            </div>
        </div>

        <div class="text-xs text-gray-500">
            Nota: los totales, direcciones e items no se editan aquí.
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.orders.show', $order) }}"
               class="text-sm text-gray-700 hover:text-gray-900 underline">
                Cancelar
            </a>

            <button type="submit" class="text-sm bg-blue-600 hover:bg-red-500 text-white rounded px-4 py-2 hover:bg-gray-800">
                Guardar cambios
            </button>
        </div>
    </form>
@endsection
