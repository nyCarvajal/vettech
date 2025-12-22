<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'VetTech') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-b from-[#ECFDF5] via-white to-[#F8FAFC] text-[#111827] antialiased">
    <div class="relative min-h-screen overflow-hidden">
        <div class="absolute inset-x-0 bottom-0 z-0 pointer-events-none">
            <svg class="w-full h-40 sm:h-56" viewBox="0 0 1440 320" preserveAspectRatio="none" aria-hidden="true">
                <path fill="#ffffff" d="M0,224L120,197.3C240,171,480,117,720,122.7C960,128,1200,192,1320,224L1440,256L1440,0L1320,0C1200,0,960,0,720,0C480,0,240,0,120,0L0,0Z"></path>
                <path fill="#ECFDF5" fill-opacity="0.75" d="M0,288L120,256C240,224,480,160,720,149.3C960,139,1200,181,1320,202.7L1440,224L1440,320L1320,320C1200,320,960,320,720,320C480,320,240,320,120,320L0,320Z"></path>
            </svg>
        </div>

        <main class="relative z-10 flex min-h-screen items-center justify-center px-4 py-12">
            <div class="w-full max-w-xl flex justify-center">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
