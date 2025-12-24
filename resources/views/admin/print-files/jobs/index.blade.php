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

        <a href="{{ route('admin.print-files.jobs.create', $printFile) }}"
           class="px-4 py-2 bg-gray-900 text-green-600 text-sm rounded hover:bg-gray-700">
            Nuevo job
        </a>
    </div>

    <div class="mt-6 bg-white border rounded-lg overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-gray-700">
            <tr>
                <th class="text-left px-4 py-3">Material</th>
                <th class="text-left px-4 py-3">Cantidad</th>
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
                        <span class="inline-flex px-2 py-1 rounded text-xs
                            @if($job->status->value === 'draft') bg-gray-100 text-gray-700
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
                        {{ number_format($job->unit_price, 2) }} €
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
                    <td colspan="6" class="px-4 py-6 text-gray-600">
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
