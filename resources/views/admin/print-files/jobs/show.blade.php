@extends('layouts.admin')

@section('content')
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                Detalle del trabajo de impresión #{{ $printJob->id }}
            </h1>
            <p class="text-sm text-gray-600 mt-1">
                Archivo: <strong>{{ $printFile->original_name }}</strong>
            </p>
        </div>

        <div class="inline-flex items-center gap-3">
            <a href="{{ route('admin.print-files.jobs.index', $printFile) }}"
               class="text-sm text-gray-700 underline">
                Volver
            </a>

            <a href="{{ route('admin.print-files.jobs.edit', [$printFile, $printJob]) }}"
               class="text-sm text-gray-700 underline">
                Editar
            </a>

            <form method="POST"
                  action="{{ route('admin.print-files.jobs.destroy', [$printFile, $printJob]) }}"
                  onsubmit="return confirm('¿Eliminar este job?')"
                  class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm text-red-600 underline">
                    Eliminar
                </button>
            </form>
        </div>
    </div>

    <div class="mt-6 bg-white border rounded-lg overflow-hidden">
        <div class="px-6 py-5 border-b bg-gray-50">
            <h2 class="text-sm font-semibold text-gray-700">Información</h2>
        </div>

        <dl class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">
            <div>
                <dt class="text-gray-500">Estado</dt>
                <dd class="mt-1">
                    <span class="inline-flex px-2 py-1 rounded text-xs
                        @if($printJob->status->value === 'draft') bg-gray-100 text-gray-700
                        @elseif($printJob->status->value === 'in_cart') bg-yellow-100 text-yellow-700
                        @elseif($printJob->status->value === 'ordered') bg-blue-100 text-blue-700
                        @elseif($printJob->status->value === 'printing') bg-purple-100 text-purple-700
                        @elseif($printJob->status->value === 'shipped') bg-indigo-100 text-indigo-700
                        @elseif($printJob->status->value === 'completed') bg-green-100 text-green-700
                        @elseif($printJob->status->value === 'cancelled') bg-red-100 text-red-700
                        @endif
                    ">
                        {{ ucfirst(str_replace('_', ' ', $printJob->status->value)) }}
                    </span>
                </dd>
            </div>

            <div>
                <dt class="text-gray-500">Material</dt>
                <dd class="mt-1 text-gray-900">
                    {{ $printJob->material->name ?? '—' }}
                </dd>
            </div>

            <div>
                <dt class="text-gray-500">Tecnología</dt>
                <dd class="mt-1 text-gray-900">{{ $printJob->technology }}</dd>
            </div>

            <div>
                <dt class="text-gray-500">Color</dt>
                <dd class="mt-1 text-gray-900">{{ $printJob->color_name ?? '—' }}</dd>
            </div>

            <div>
                <dt class="text-gray-500">Cantidad</dt>
                <dd class="mt-1 text-gray-900">{{ $printJob->quantity }}</dd>
            </div>

            <div>
                <dt class="text-gray-500">Precio unitario</dt>
                <dd class="mt-1 text-gray-900">{{ number_format($printJob->unit_price, 2) }} €</dd>
            </div>

            <div>
                <dt class="text-gray-500">Material estimado (g)</dt>
                <dd class="mt-1 text-gray-900">{{ $printJob->estimated_material_g ?? '—' }}</dd>
            </div>

            <div>
                <dt class="text-gray-500">Tiempo estimado (min)</dt>
                <dd class="mt-1 text-gray-900">{{ $printJob->estimated_time_min ?? '—' }}</dd>
            </div>

            <div>
                <dt class="text-gray-500">Creado</dt>
                <dd class="mt-1 text-gray-900">{{ $printJob->created_at->format('d/m/Y H:i') }}</dd>
            </div>

            <div>
                <dt class="text-gray-500">Actualizado</dt>
                <dd class="mt-1 text-gray-900">{{ $printJob->updated_at->format('d/m/Y H:i') }}</dd>
            </div>
        </dl>

        <div class="px-6 py-5 border-t">
            <h3 class="text-sm font-semibold text-gray-700">Pricing breakdown</h3>
            <div class="mt-2 text-sm text-gray-700">
                @if(!empty($printJob->pricing_breakdown))
                    <pre class="bg-gray-50 border rounded p-3 overflow-auto">{{ json_encode($printJob->pricing_breakdown, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                @else
                    <span class="text-gray-500">—</span>
                @endif
            </div>
        </div>
    </div>
@endsection
