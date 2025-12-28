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
                <dt class="text-gray-900">Estado</dt>
                <dd class="mt-1">
                    <span class="inline-flex px-2 py-1 rounded text-xs
                        @if($printJob->status->value === 'draft') bg-gray-100 text-gray-700
                        @elseif($printJob->status->value === 'priced') bg-gray-100 text-gray-700
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
                <dt class="text-gray-900">Material</dt>
                <dd class="mt-1 text-gray-500">
                    {{ $printJob->material->name ?? '—' }}
                </dd>
            </div>

            <div>
                <dt class="text-gray-900">Tecnología</dt>
                <dd class="mt-1 text-gray-500">{{ $printJob->technology }}</dd>
            </div>

            <div>
                <dt class="text-gray-900">Color</dt>
                <dd class="mt-1 text-gray-500">{{ $printJob->color_name ?? '—' }}</dd>
            </div>

            <div>
                <dt class="text-gray-900">Cantidad</dt>
                <dd class="mt-1 text-gray-500">{{ $printJob->quantity }}</dd>
            </div>

            <div>
                <dt class="text-gray-900">Precio unitario</dt>
                <dd class="mt-1 text-gray-500">
                    @if(is_null($printJob->unit_price))
                        <span class="text-gray-500">Pendiente de cálculo</span>
                    @else
                        {{ number_format((float) $printJob->unit_price, 2) }} €
                    @endif
                </dd>
            </div>

            <div>
                <dt class="text-gray-900">Material estimado (g)</dt>
                <dd class="mt-1 text-gray-500">
                    @if(is_null($printJob->estimated_material_g))
                        <span class="text-gray-500">Pendiente de cálculo</span>
                    @else
                        {{ $printJob->estimated_material_g }}
                    @endif
                </dd>
            </div>

            <div>
                <dt class="text-gray-900">Tiempo estimado (min)</dt>
                <dd class="mt-1 text-gray-500">
                    @if(is_null($printJob->estimated_time_min))
                        <span class="text-gray-500">Pendiente de cálculo</span>
                    @else
                        {{ $printJob->estimated_time_min }}
                    @endif
                </dd>
            </div>

            <div>
                <dt class="text-gray-900">Creado</dt>
                <dd class="mt-1 text-gray-500">{{ $printJob->created_at->format('d/m/Y H:i') }}</dd>
            </div>

            <div>
                <dt class="text-gray-900">Actualizado</dt>
                <dd class="mt-1 text-gray-500">{{ $printJob->updated_at->format('d/m/Y H:i') }}</dd>
            </div>
        </dl>

        {{-- Este botón (solo-Admin) simula disparar el pricing real del printJob, lo añade al carrito del
         propietario y permite validar el flujo end-to-end sin frontend--}}

        @if(
    in_array($printJob->status->value, ['draft', 'priced'], true)
    )
            <div class="px-6 py-5 border-t bg-gray-50">
                <form method="POST"
                      action="{{ route('admin.users.cart.items.print-jobs.store', [$printJob->user_id, $printJob]) }}">
                    @csrf

                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-red-500">
                        Añadir al carrito (calcular precio)
                    </button>

                    <p class="mt-2 text-xs text-gray-500">
                        El precio se calculará automáticamente antes de añadir el trabajo al carrito.
                    </p>
                </form>
            </div>
        @endif


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
