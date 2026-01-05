<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Lexxis') }} · Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
<div class="min-h-screen">
    <header class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.dashboard') }}" class="font-semibold text-gray-900">
                    Admin · Lexxis
                </a>

                <nav class="hidden sm:flex items-center gap-3 text-sm text-gray-600">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
                    <a href="{{ route('admin.orders.index') }}" class="hover:text-gray-900">Pedidos</a>
                    <a href="{{ route('admin.users.index') }}" class="hover:text-gray-900">Usuarios</a>
                    <a href="{{ route('admin.products.index' )}}" class="hover:text-gray-900">Productos</a>
                    <a href="{{ route('admin.materials.index') }}" class="hover:text-gray-900">Materiales</a>
                    <a href="{{ route('admin.print-files.index') }}" class="hover:text-gray-900">Archivos imprimibles</a>
                </nav>
            </div>

            <div class="flex items-center gap-3 text-sm">
                <span class="text-gray-600">{{ auth()->user()->name }} ({{ auth()->user()->role->value }})</span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-600 hover:text-gray-900 underline">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if (session('success'))
            <div class="mb-4 rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif

            @if (session('error'))
                <div class="mb-4 rounded border border-red-300 text-red-600 bg-red-100 px-4 py-3 text-sm text-red-900">
                    <strong>Error:</strong> {{ session('error') }}
                </div>
            @endif


            @yield('content')
    </main>


</div>
</body>
</html>
