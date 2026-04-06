@extends('layouts.admin')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                Trabajos pendientes de revisión
            </h1>
            <p class="text-sm text-gray-600 mt-1">
                Trabajos de impresión que requieren validación manual antes de quedar listos para compra.
            </p>
        </div>

        <a href="{{ route('admin.print-files.index') }}"
           class="text-sm text-gray-700 underline">
            Ver archivos imprimibles
        </a>
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
                <th class="text-left px-4 py-3">Job</th>
                <th class="text-left px-4 py-3">Usuario</th>
                <th class="text-left px-4 py-3">Archivo</th>
                <th class="text-left px-4 py-3">Material</th>
                <th class="text-left px-4 py-3">Precio</th>
                <th class="text-left px-4 py-3">Estado</th>
                <th class="text-right px-4 py-3">Acciones</th>
            </tr>
            </thead>

            <tbody class="divide-y">
            @forelse($printJobs as $job)
                <tr>
                    <td class="px-4 py-3 text-gray-900 font-medium">
                        #{{ $job->id }}
                    </td>

                    <td class="px-4 py-3 text-gray-700">
                        <div>{{ $job->user->name ?? '—' }} {{ $job->user->last_name ?? '' }}</div>
                        <div class="text-xs text-gray-500">{{ $job->user->email ?? '—' }}</div>
                    </td>

                    <td class="px-4 py-3 text-gray-700">
                        <a href="{{ route('admin.print-files.jobs.show', [$job->printFile, $job]) }}"
                           class="underline text-gray-700 hover:text-gray-900">
                            {{ $job->printFile->original_name ?? '—' }}
                        </a>
                    </td>

                    <td class="px-4 py-3 text-gray-700">
                        {{ $job->material->name ?? '—' }}
                    </td>

                    <td class="px-4 py-3 text-gray-700">
                        @if(is_null($job->unit_price))
                            <span class="text-gray-500">Pendiente</span>
                        @else
                            {{ number_format((float) $job->unit_price, 2) }} €
                        @endif
                    </td>

                    <td class="px-4 py-3">
                        <span class="inline-flex px-2 py-1 rounded text-xs bg-orange-100 text-orange-700">
                            {{ ucfirst(str_replace('_', ' ', $job->status->value)) }}
                        </span>
                    </td>

                    <td class="px-4 py-3 text-right">
                        <div class="inline-flex items-center gap-3">
                            <a href="{{ route('admin.print-files.jobs.show', [$job->printFile, $job]) }}"
                               class="text-sm text-gray-700 underline">
                                Ver
                            </a>

                            <a href="{{ route('admin.print-files.jobs.edit', [$job->printFile, $job]) }}"
                               class="text-sm text-gray-700 underline">
                                Editar
                            </a>

                            <form method="POST"
                                  action="{{ route('admin.print-files.jobs.approve-review', [$job->printFile, $job]) }}"
                                  onsubmit="return confirm('¿Aprobar la revisión manual de este trabajo?')"
                                  class="inline">
                                @csrf
                                <button type="submit"
                                        class="text-sm text-green-700 underline">
                                    Aprobar revisión
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-6 text-gray-600">
                        No hay trabajos pendientes de revisión.
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
