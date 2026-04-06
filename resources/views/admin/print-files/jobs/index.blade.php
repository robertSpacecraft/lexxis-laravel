@extends('layouts.admin')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                Trabajos de impresión
            </h1>
            <p class="text-sm text-gray-600 mt-1">
                Archivo: <strong>{{ $printFile->original_name }}</strong>
            </p>
        </div>

        <div class="inline-flex items-center gap-3">
            <a href="{{ route('admin.print-jobs.review-pending') }}"
               class="text-sm text-gray-700 underline">
                Pendientes de revisión
            </a>

            <a href="{{ route('admin.print-files.jobs.create', $printFile) }}"
               class="px-4 py-2 bg-gray-900 text-green-600 text-sm rounded hover:bg-gray-700">
                Nuevo job
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mt-4 p-3 rounded-md bg-green-50 border border-green-200 text-green-800 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mt-4 p-3 rounded-md bg-red-50 border border-red-200 text-red-800 text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="mt-6 bg-white border rounded-lg overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-gray-700">
            <tr>
                <th class="text-left px-4 py-3">Material</th>
                <th class="text-left px-4 py-3">Cantidad</th>
                <th class="text-left px-4 py-3">Relleno</th>
                <th class="text-left px-4 py-3">Escala</th>
                <th class="text-left px-4 py-3">Estado</th>
                <th class="text-left px-4 py-3">Precio unit.</th>
                <th class="text-left px-4 py-3">Fecha</th>
                <th class="text-right px-4 py-3">Acciones</th>
            </tr>
            </thead>

            <tbody class="divide-y">
            @forelse($printJobs as $job)
                <tr>
                    <td class="px-4 py-3">
                        {{ $job->material->name ?? '—' }}
                    </td>

                    <td class="px-4 py-3">
                        {{ $job->quantity }}
                    </td>

                    <td class="px-4 py-3">
                        {{ $job->infill_percent ?? '—' }}%
                    </td>

                    <td class="px-4 py-3">
                        {{ $job->scale_percent ?? '—' }}%
                    </td>

                    <td class="px-4 py-3">
                        <span class="inline-flex px-2 py-1 rounded text-xs
                            @if($job->status->value === 'draft') bg-gray-100 text-gray-700
                            @elseif($job->status->value === 'review_pending') bg-orange-100 text-orange-700
                            @elseif($job->status->value === 'priced') bg-emerald-100 text-emerald-700
                            @elseif($job->status->value === 'in_cart') bg-yellow-100 text-yellow-700
                            @elseif($job->status->value === 'ordered') bg-blue-100 text-blue-700
                            @elseif($job->status->value === 'printing') bg-purple-100 text-purple-700
                            @elseif($job->status->value === 'shipped') bg-indigo-100 text-indigo-700
                            @elseif($job->status->value === 'completed') bg-green-100 text-green-700
                            @elseif($job->status->value === 'cancelled') bg-red-100 text-red-700
                            @endif
                        ">
                            {{ ucfirst(str_replace('_', ' ', $job->status->value)) }}
                        </span>
                    </td>

                    <td class="px-4 py-3 text-gray-700">
                        @if(is_null($job->unit_price))
                            <span class="text-gray-500">Pendiente</span>
                        @else
                            {{ number_format((float) $job->unit_price, 2) }} €
                        @endif
                    </td>

                    <td class="px-4 py-3 text-gray-600">
                        {{ $job->created_at->format('d/m/Y H:i') }}
                    </td>

                    <td class="px-4 py-3 text-right">
                        <div class="inline-flex items-center gap-3">
                            <a href="{{ route('admin.print-files.jobs.show', [$printFile, $job]) }}"
                               class="text-sm text-gray-700 underline">
                                Ver
                            </a>

                            <a href="{{ route('admin.print-files.jobs.edit', [$printFile, $job]) }}"
                               class="text-sm text-gray-700 underline">
                                Editar
                            </a>

                            @if($job->status->value === 'review_pending')
                                <form method="POST"
                                      action="{{ route('admin.print-files.jobs.approve-review', [$printFile, $job]) }}"
                                      onsubmit="return confirm('¿Aprobar la revisión manual de este trabajo?')"
                                      class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="text-sm text-green-700 underline">
                                        Aprobar revisión
                                    </button>
                                </form>
                            @endif

                            <form method="POST"
                                  action="{{ route('admin.print-files.jobs.destroy', [$printFile, $job]) }}"
                                  onsubmit="return confirm('¿Eliminar este job?')"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-sm text-red-600 underline">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-6 text-gray-600">
                        No hay trabajos de impresión para este archivo.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $printJobs->links() }}
    </div>
@endsection
