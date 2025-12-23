@extends('layouts.admin')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                Archivos imprimibles
            </h1>
        </div>
        <a href="{{ route('admin.print-files.create') }}"
           class="px-4 py-2 bg-gray-900 text-green-600 text-sm rounded hover:bg-gray-700">
            Upload
        </a>
    </div>


    <div class="mt-6 bg-white border rounded-lg overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-gray-700">
            <tr>
                <th class="text-left px-4 py-3">Usuario</th>
                <th class="text-left px-4 py-3">Archivo</th>
                <th class="text-left px-4 py-3">Tamaño</th>
                <th class="text-left px-4 py-3">Estado</th>
                <th class="text-left px-4 py-3">Fecha</th>
                <th class="text-right px-4 py-3">Acciones</th>
            </tr>
            </thead>

            <tbody class="divide-y">
            @forelse($printFiles as $file)
                <tr>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900">
                            {{ $file->user->name }} {{ $file->user->last_name ?? '' }}
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $file->user->email }}
                        </div>
                    </td>

                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900">
                            {{ $file->original_name }}
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $file->file_extension ?? '—' }}
                        </div>
                    </td>

                    <td class="px-4 py-3 text-gray-600">
                        @if($file->file_size)
                            {{ number_format($file->file_size / 1024 / 1024, 2) }} MB
                        @else
                            —
                        @endif
                    </td>

                    <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-1 rounded text-xs
                                @if($file->status->value === 'uploaded') bg-gray-100 text-gray-700
                                @elseif($file->status->value === 'reviewed') bg-blue-100 text-blue-700
                                @elseif($file->status->value === 'approved') bg-green-100 text-green-700
                                @elseif($file->status->value === 'rejected') bg-red-100 text-red-700
                                @endif
                            ">
                                {{ ucfirst($file->status->value) }}
                            </span>
                    </td>

                    <td class="px-4 py-3 text-gray-600">
                        {{ $file->created_at->format('d/m/Y H:i') }}
                    </td>

                    <td class="px-4 py-3 text-right">
                        <div class="inline-flex items-center gap-3">
                            <a href="{{ route('admin.print-files.download', $file) }}"
                               class="text-sm text-gray-700 underline">
                                Descargar
                            </a>

                            <a href="{{ route('admin.print-files.show', $file) }}"
                               class="text-sm text-gray-700 underline">
                                Ver
                            </a>

                            <a href="{{ route('admin.print-files.edit', $file) }}"
                               class="text-sm text-gray-700 underline">
                                Editar
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-gray-600">
                        No hay archivos imprimibles registrados.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $printFiles->links() }}
    </div>
@endsection
