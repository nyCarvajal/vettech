<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'VetTech') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/scss/icons.scss', 'resources/scss/style.scss', 'resources/sass/app.scss'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-md bg-white border border-gray-200 rounded-2xl shadow-soft p-8 space-y-6">
            <div class="text-center space-y-2">
                <div class="mx-auto h-12 w-12 rounded-full bg-mint-50 text-mint-600 flex items-center justify-center font-semibold">VT</div>
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">{{ config('app.name', 'VetTech') }}</h1>
                    <p class="text-sm text-gray-600">Ingreso seguro al sistema clínico</p>
                </div>
            </div>

            @if (session('status'))
                <x-alert type="success">{{ session('status') }}</x-alert>
            @endif

            {{ $slot }}
        </div>
    </div>
</body>
</html>
