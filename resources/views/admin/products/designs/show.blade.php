@extends('layouts.admin')

@section('content')
    <div class="bg-white shadow-sm rounded-lg p-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between mb-6">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">
                    Diseño #{{ $design->id }} · {{ $product->name }}
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    Detalle completo del diseño personalizado.
                </p>
            </div>

            <div class="flex flex-wrap gap-3 text-sm">
                <a href="{{ route('admin.products.designs.index', $product) }}" class="underline text-gray-700 hover:text-gray-900">
                    Volver a diseños del producto
                </a>
                <a href="{{ route('admin.products.designs.edit', [$product, $design]) }}" class="underline text-gray-700 hover:text-gray-900">
                    Editar diseño
                </a>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded border p-4">
                <h2 class="font-semibold text-gray-900 mb-4">Datos principales</h2>

                <dl class="grid gap-3 text-sm">
                    <div>
                        <dt class="text-gray-500">Producto</dt>
                        <dd class="text-gray-900 font-medium">{{ $design->product?->name ?? '—' }}</dd>
                    </div>

                    <div>
                        <dt class="text-gray-500">Usuario</dt>
                        <dd class="text-gray-900 font-medium">
                            @if($design->user)
                                <a href="{{ route('admin.users.show', $design->user) }}" class="underline hover:text-gray-700">
                                    {{ $design->user->name }} {{ $design->user->last_name }}
                                </a>
                                <div class="text-xs text-gray-600">{{ $design->user->email }}</div>
                                <div class="text-xs text-gray-600">{{ $design->user->phone }}</div>
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-gray-500">Material</dt>
                        <dd class="text-gray-900">{{ $design->material?->name ?? '—' }}</dd>
                    </div>

                    <div>
                        <dt class="text-gray-500">Color</dt>
                        <dd class="text-gray-900">{{ $design->color_name ?? '—' }}</dd>
                    </div>

                    <div>
                        <dt class="text-gray-500">Talla EU</dt>
                        <dd class="text-gray-900">{{ $design->size_eu ?? '—' }}</dd>
                    </div>

                    <div>
                        <dt class="text-gray-500">Precio unitario</dt>
                        <dd class="text-gray-900">{{ $design->unit_price !== null ? number_format((float)$design->unit_price, 2, ',', '.') . ' €' : '—' }}</dd>
                    </div>

                    <div>
                        <dt class="text-gray-500">Estado</dt>
                        <dd>
                            <span class="inline-flex rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-700">
                                {{ $design->status?->value ?? $design->status }}
                            </span>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-gray-500">Creado</dt>
                        <dd class="text-gray-900">{{ optional($design->created_at)->format('d/m/Y H:i') }}</dd>
                    </div>

                    <div>
                        <dt class="text-gray-500">Actualizado</dt>
                        <dd class="text-gray-900">{{ optional($design->updated_at)->format('d/m/Y H:i') }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded border p-4">
                <h2 class="font-semibold text-gray-900 mb-4">Pricing breakdown</h2>

                @if(!empty($design->pricing_breakdown))
                    <pre class="text-xs bg-gray-50 border rounded p-4 overflow-auto">{{ json_encode($design->pricing_breakdown, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                @else
                    <p class="text-sm text-gray-600">Este diseño no tiene breakdown registrado.</p>
                @endif
            </div>
        </div>

        <div class="mt-6 flex items-center gap-4">
            <a href="{{ route('admin.products.designs.edit', [$product, $design]) }}"
               class="inline-flex items-center rounded bg-gray-900 px-4 py-2 text-white text-sm hover:bg-gray-800">
                Editar
            </a>

            <form method="POST" action="{{ route('admin.products.designs.destroy', [$product, $design]) }}"
                  onsubmit="return confirm('¿Seguro que quieres eliminar este diseño?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center rounded border border-red-300 px-4 py-2 text-red-700 text-sm hover:bg-red-50">
                    Eliminar
                </button>
            </form>
        </div>
    </div>
@endsection
