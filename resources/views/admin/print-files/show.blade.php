@extends('layouts.admin')

@section('content')
    <div class="flex items-start justify-between gap-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Detalle archivo imprimible</h1>
            <p class="mt-1 text-sm text-gray-600">
                {{ $printFile->original_name }}
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.print-files.index') }}"
               class="text-sm text-gray-600 hover:text-gray-900 underline">
                Volver
            </a>

            <a href="{{ route('admin.print-files.download', $printFile) }}"
               class="px-4 py-2 bg-gray-900 text-green-600 text-sm rounded hover:bg-gray-700">
                Descargar
            </a>

            <a href="{{ route('admin.print-files.edit', $printFile) }}"
               class="px-4 py-2 border border-gray-300 text-gray-700 text-sm rounded hover:bg-gray-50">
                Editar
            </a>
            <form method="POST"
                  action="{{ route('admin.print-files.destroy', $printFile) }}"
                  onsubmit="return confirm('¿Seguro que quieres eliminar este archivo? Se borrará también del almacenamiento.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-4 py-2 text-sm rounded border border-red-300 text-red-700 hover:bg-red-50">
                    Eliminar
                </button>
            </form>

        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white border rounded-lg p-6">
            <h2 class="text-sm font-semibold text-gray-900">Datos</h2>

            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-600">ID</dt>
                    <dd class="text-gray-900 font-medium">{{ $printFile->id }}</dd>
                </div>

                <div class="flex justify-between gap-4">
                    <dt class="text-gray-600">Usuario (owner)</dt>
                    <dd class="text-gray-900 font-medium">#{{ $printFile->user_id }}</dd>
                </div>

                <div class="flex justify-between gap-4">
                    <dt class="text-gray-600">Estado</dt>
                    <dd class="text-gray-900 font-medium">{{ $printFile->status?->value ?? $printFile->status }}</dd>
                </div>

                <div class="flex justify-between gap-4">
                    <dt class="text-gray-600">Extensión</dt>
                    <dd class="text-gray-900 font-medium">{{ $printFile->file_extension ?? '—' }}</dd>
                </div>

                <div class="flex justify-between gap-4">
                    <dt class="text-gray-600">MIME</dt>
                    <dd class="text-gray-900 font-medium">{{ $printFile->mime_type ?? '—' }}</dd>
                </div>

                <div class="flex justify-between gap-4">
                    <dt class="text-gray-600">Tamaño</dt>
                    <dd class="text-gray-900 font-medium">
                        {{ $printFile->file_size ? number_format($printFile->file_size / 1024 / 1024, 2) . ' MB' : '—' }}
                    </dd>
                </div>

                <div class="pt-2">
                    <dt class="text-gray-600">Ruta (storage)</dt>
                    <dd class="mt-1 text-gray-900 break-all">
                        {{ $printFile->storage_path ?? '—' }}
                    </dd>
                </div>

                <div class="pt-2">
                    <dt class="text-gray-600">Notas</dt>
                    <dd class="mt-1 text-gray-900 whitespace-pre-wrap">
                        {{ $printFile->notes ?: '—' }}
                    </dd>
                </div>
            </dl>
        </div>

        <div class="bg-white border rounded-lg p-6">
            <h2 class="text-sm font-semibold text-gray-900">Metadata</h2>

            <div class="mt-4 text-sm text-gray-900">
                @if(!empty($printFile->metadata))
                    <pre class="text-xs bg-gray-50 border rounded p-4 overflow-auto">{{ json_encode($printFile->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                @else
                    <p class="text-gray-600">—</p>
                @endif
            </div>
        </div>
    </div>
@endsection
