<!DOCTYPE html>
<html lang="es">
<head>
    @php use Illuminate\Support\Str; @endphp
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'VetTech') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
    @php
        $hasViteAssets = file_exists(public_path('hot')) || file_exists(public_path('build/manifest.json'));
    @endphp
    @if($hasViteAssets)
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/scss/icons.scss', 'resources/scss/style.scss', 'resources/sass/app.scss'])
    @else
        <!-- Fallback CSS y JS para cuando Vite no está disponible -->
        <link rel="stylesheet" href="{{ asset('css/app-fallback.css') }}">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    @endif

    <!-- Fallback CSS para cuando Vite no está disponible o el bundle no carga -->
    <link rel="stylesheet" href="{{ asset('css/app-fallback.css') }}">
    <noscript>
        <link rel="stylesheet" href="{{ asset('css/app-fallback.css') }}">
    </noscript>
    <script>
        window.addEventListener('load', function () {
            const mint600 = getComputedStyle(document.documentElement).getPropertyValue('--mint-600');
            const hasMint = mint600 && mint600.trim().length > 0;
            if (!hasMint) {
                const fallbackLink = document.createElement('link');
                fallbackLink.rel = 'stylesheet';
                fallbackLink.href = '{{ asset('css/app-fallback.css') }}';
                document.head.appendChild(fallbackLink);
            }
        });
    </script>

    @stack('styles')
</head>
<body class="bg-gray-50">
    <div id="app" class="min-h-screen flex flex-col">
        <header class="bg-white border-b border-gray-200">
            <div class="h-16 px-6 flex items-center justify-between gap-6">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-lg bg-mint-50 text-mint-600 flex items-center justify-center font-semibold">VT</div>
                    <div>
                        <p class="text-lg font-semibold text-gray-900">{{ config('app.name', 'VetTech') }}</p>
                        <p class="text-xs text-gray-500">Clínica veterinaria</p>
                    </div>
                </div>
                <div class="hidden md:flex items-center gap-2 text-sm text-gray-500">
                    @yield('breadcrumbs')
                </div>
                <div class="flex items-center gap-3">
                    @auth
                        <div class="relative">
                            <button class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="h-8 w-8 rounded-full bg-mint-50 text-mint-600 flex items-center justify-center font-semibold">{{ strtoupper(Str::substr(Auth::user()->name, 0, 1)) }}</span>
                                <span class="text-sm font-medium">{{ Auth::user()->name }}</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end mt-2 rounded-xl shadow-soft border border-gray-100 py-2 text-sm" aria-label="Menú de usuario">
                                <a class="dropdown-item" href="{{ Route::has('profile.edit') ? route('profile.edit') : '#' }}">Perfil</a>
                                <div class="dropdown-divider"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger-500">Cerrar sesión</button>
                                </form>
                            </div>
                        </div>
                    @endauth
                </div>
            </div>
        </header>

        <div class="flex flex-1">
            <aside class="hidden lg:block w-64 bg-white border-r border-gray-200">
                @php
                    $navItems = [
                        ['label' => 'Dashboard', 'route' => 'dashboard'],
                        ['label' => 'Tutores', 'route' => 'owners.index'],
                        ['label' => 'Pacientes', 'route' => 'patients.index'],
                        ['label' => 'Sala de belleza', 'route' => 'groomings.index'],
                        ['label' => 'Agenda', 'route' => 'reservas.index'],
                        ['label' => 'Hospitalización 24/7', 'route' => 'hospital.board'],
                        ['label' => 'Dispensación', 'route' => 'dispensations.index'],
                        ['label' => 'Ventas', 'route' => 'sales.index'],
                        ['label' => 'Caja', 'route' => 'cash.sessions.index'],
                        ['label' => 'Reportes', 'route' => 'kardex.index'],
                    ];
                @endphp
                <nav class="p-4 space-y-1">
                    @foreach($navItems as $item)
                        @php
                            $isAvailable = isset($item['route']) && Route::has($item['route']);
                            $url = $isAvailable ? route($item['route']) : '#';
                            $active = $isAvailable && request()->routeIs($item['route'] . '*');
                        @endphp
                        <a href="{{ $url }}" class="sidebar-link {{ $active ? 'sidebar-link-active' : '' }}">
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </nav>
            </aside>

            <main class="flex-1">
                <div class="max-w-7xl mx-auto px-6 py-8 space-y-4">
                    @if(session('status'))
                        <x-alert type="success">{{ session('status') }}</x-alert>
                    @endif
                    @if(session('success'))
                        <x-alert type="success">{{ session('success') }}</x-alert>
                    @endif
                    @if(session('error'))
                        <x-alert type="error">{{ session('error') }}</x-alert>
                    @endif
                    @if(session('info'))
                        <x-alert type="info">{{ session('info') }}</x-alert>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
