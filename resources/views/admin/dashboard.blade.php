@extends('layouts.admin')

@section('content')
    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        <a href="{{ route('admin.products.index') }}"
           class="bg-white shadow-sm rounded-lg p-6 border hover:border-gray-400 transition">
            <h2 class="text-lg font-semibold text-gray-900">Productos</h2>
            <p class="mt-2 text-sm text-gray-600">
                Gestiona productos, variantes e imágenes del catálogo.
            </p>
        </a>

        <a href="{{ route('admin.product-designs.index') }}"
           class="bg-white shadow-sm rounded-lg p-6 border hover:border-gray-400 transition">
            <h2 class="text-lg font-semibold text-gray-900">Diseños personalizados</h2>
            <p class="mt-2 text-sm text-gray-600">
                Revisa diseños creados por usuarios, su estado y sus configuraciones.
            </p>
        </a>

        <a href="{{ route('admin.orders.index') }}"
           class="bg-white shadow-sm rounded-lg p-6 border hover:border-gray-400 transition">
            <h2 class="text-lg font-semibold text-gray-900">Pedidos</h2>
            <p class="mt-2 text-sm text-gray-600">
                Consulta y actualiza el estado global de los pedidos.
            </p>
        </a>

        <a href="{{ route('admin.users.index') }}"
           class="bg-white shadow-sm rounded-lg p-6 border hover:border-gray-400 transition">
            <h2 class="text-lg font-semibold text-gray-900">Usuarios</h2>
            <p class="mt-2 text-sm text-gray-600">
                Accede a usuarios, direcciones, carrito y pedidos asociados.
            </p>
        </a>

        <a href="{{ route('admin.materials.index') }}"
           class="bg-white shadow-sm rounded-lg p-6 border hover:border-gray-400 transition">
            <h2 class="text-lg font-semibold text-gray-900">Materiales</h2>
            <p class="mt-2 text-sm text-gray-600">
                Mantén los materiales disponibles y sus propiedades.
            </p>
        </a>

        <a href="{{ route('admin.print-files.index') }}"
           class="bg-white shadow-sm rounded-lg p-6 border hover:border-gray-400 transition">
            <h2 class="text-lg font-semibold text-gray-900">Archivos imprimibles</h2>
            <p class="mt-2 text-sm text-gray-600">
                Supervisa archivos de impresión y sus trabajos asociados.
            </p>
        </a>

        <a href="{{ route('admin.print-jobs.review-pending') }}"
           class="bg-white shadow-sm rounded-lg p-6 border hover:border-gray-400 transition">
            <h2 class="text-lg font-semibold text-gray-900">Pendientes de revisión</h2>
            <p class="mt-2 text-sm text-gray-600">
                Valida manualmente trabajos de impresión que no han podido valorarse con suficiente fiabilidad.
            </p>
        </a>
    </div>
@endsection
