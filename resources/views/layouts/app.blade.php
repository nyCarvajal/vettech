<!DOCTYPE html>
<html lang="es">
<head>
    @php use Illuminate\Support\Str; @endphp
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'VetTech') }}</title>
    @php
        $hasViteAssets = file_exists(public_path('hot')) || file_exists(public_path('build/manifest.json'));
    @endphp
    @if($hasViteAssets)
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/scss/icons.scss', 'resources/scss/style.scss', 'resources/sass/app.scss'])
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
<body class="relative min-h-screen bg-slate-50 text-gray-900">
    <div class="pointer-events-none fixed inset-0 overflow-hidden">
        <div class="absolute -top-24 -left-20 h-64 w-64 rounded-full bg-gradient-to-br from-purple-300/30 via-purple-200/20 to-emerald-200/30 blur-3xl"></div>
        <div class="absolute bottom-0 right-0 h-72 w-72 rounded-full bg-gradient-to-tr from-emerald-200/25 via-white/40 to-purple-200/30 blur-3xl"></div>
    </div>

    <div id="app" class="relative flex min-h-screen flex-col">
        <header class="bg-white/90 backdrop-blur-lg shadow-sm ring-1 ring-slate-100">
            <div class="flex h-16 items-center justify-between gap-6 px-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500 via-purple-400 to-emerald-400 text-sm font-semibold text-white shadow-md shadow-purple-200/60">VT</div>
                    <div>
                        <p class="text-lg font-semibold text-gray-900">{{ config('app.name', 'VetTech') }}</p>
                        <p class="text-xs text-gray-500">Clínica veterinaria</p>
                    </div>
                </div>
                <div class="hidden items-center gap-2 text-sm text-gray-500 md:flex">
                    @yield('breadcrumbs')
                </div>
                <div class="flex items-center gap-3">
                    @auth
                        <div class="relative">
                            <button class="inline-flex items-center gap-2 rounded-xl border border-gray-200/80 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm hover:border-purple-200 focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:ring-offset-2" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-purple-500/90 via-purple-400 to-emerald-400 text-sm font-semibold text-white shadow-sm">{{ strtoupper(Str::substr(Auth::user()->name, 0, 1)) }}</span>
                                <span class="text-sm font-medium">{{ Auth::user()->name }}</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end mt-2 w-44 rounded-2xl border border-gray-100/80 bg-white/90 py-2 text-sm shadow-soft backdrop-blur">
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
            <aside class="hidden w-72 border-r border-slate-200/80 bg-white/90 px-4 py-6 backdrop-blur lg:block">
                @php
                    $navSections = [
                        'Clínica' => [
                            ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'home'],
                            ['label' => 'Tutores', 'route' => 'owners.index', 'icon' => 'users'],
                            ['label' => 'Pacientes', 'route' => 'patients.index', 'icon' => 'paw'],
                        ],
                        'Operación' => [
                            ['label' => 'Agenda', 'route' => 'reservas.index', 'icon' => 'calendar'],
                            ['label' => 'Hospitalización 24/7', 'route' => 'hospital.board', 'icon' => 'bell'],
                            ['label' => 'Dispensación', 'route' => 'dispensations.index', 'icon' => 'document'],
                            ['label' => 'Sala de belleza', 'route' => 'groomings.index', 'icon' => 'sparkles'],
                        ],
                        'Finanzas' => [
                            ['label' => 'Ventas', 'route' => 'sales.index', 'icon' => 'chart-bar'],
                            ['label' => 'Caja', 'route' => 'cash.sessions.index', 'icon' => 'currency'],
                            ['label' => 'Reportes', 'route' => 'kardex.index', 'icon' => 'report'],
                        ],
                    ];

                    $icons = [
                        'home' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l8.485-8.485a2 2 0 012.83 0L23 12M5 10v8.5A1.5 1.5 0 006.5 20h3A1.5 1.5 0 0011 18.5V15a1 1 0 011-1h0a1 1 0 011 1v3.5A1.5 1.5 0 0014.5 20h3A1.5 1.5 0 0019 18.5V10"/></svg>',
                        'users' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 14a4 4 0 10-8 0m8 0a4 4 0 11-8 0m8 0v1a2 2 0 002 2h1m-3-3v1a2 2 0 01-2 2H9m-3 0a2 2 0 01-2-2v-1m0 0a4 4 0 018 0"/></svg>',
                        'paw' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M11.25 10.5c.414 0 .75.336.75.75v.75h.75a.75.75 0 010 1.5H12v.75a.75.75 0 01-1.5 0V13.5h-.75a.75.75 0 010-1.5h.75v-.75c0-.414.336-.75.75-.75z"/><path d="M7.5 5.25A1.75 1.75 0 109.25 7 1.75 1.75 0 007.5 5.25zm9 0A1.75 1.75 0 1114.75 7 1.75 1.75 0 0116.5 5.25zM5 9.5A1.75 1.75 0 116.75 11.25 1.75 1.75 0 015 9.5zm14 0A1.75 1.75 0 1117.25 11.25 1.75 1.75 0 0119 9.5z"/><path d="M12.12 9.5c-1.815-.048-3.203 1.305-3.579 2.697-.26.975-.034 1.92.56 2.686.514.668 1.306 1.173 2.25 1.294.021.003.043.003.064.003 1.323 0 2.719-.858 2.999-2.23.275-1.342-.195-2.472-.73-3.156-.45-.58-1.322-1.27-1.564-1.294z"/></svg>',
                        'calendar' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M4.5 9.75h15M5.25 6.75h13.5a1.5 1.5 0 011.5 1.5v10.5a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5V8.25a1.5 1.5 0 011.5-1.5z"/></svg>',
                        'bell' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.25 18.75a2.25 2.25 0 11-4.5 0"/><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 9.75A7.5 7.5 0 1119 9.75c0 1.69.429 3.35 1.245 4.832a.75.75 0 01-.66 1.118H3.915a.75.75 0 01-.66-1.118A11.286 11.286 0 004.5 9.75z"/></svg>',
                        'document' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M9 8h3m3 0h.01M7.5 3.75h9a2.25 2.25 0 012.25 2.25v12a2.25 2.25 0 01-2.25 2.25h-9A2.25 2.25 0 015.25 18V6A2.25 2.25 0 017.5 3.75z"/></svg>',
                        'sparkles' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 6.75l.964-2.893a.375.375 0 01.705 0l.964 2.893a3.75 3.75 0 002.358 2.358l2.893.964a.375.375 0 010 .705l-2.893.964a3.75 3.75 0 00-2.358 2.358l-.964 2.893a.375.375 0 01-.705 0l-.964-2.893a3.75 3.75 0 00-2.358-2.358l-2.893-.964a.375.375 0 010-.705l2.893-.964a3.75 3.75 0 002.358-2.358z"/></svg>',
                        'chart-bar' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 19.5h18M7.5 16.5V10.5M12 16.5V7.5M16.5 16.5v-6"/></svg>',
                        'currency' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m0 0c-3 0-5.25-1.5-5.25-4.5S9 9 12 9m0 9c3 0 5.25-1.5 5.25-4.5S15 9 12 9"/></svg>',
                        'report' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-3-3v6m-6.75 6h9.563a2.25 2.25 0 001.591-.659l3.727-3.727A2.25 2.25 0 0021 14.952V5.25A2.25 2.25 0 0018.75 3H6.75A2.25 2.25 0 004.5 5.25v13.5A2.25 2.25 0 006.75 21z"/></svg>',
                    ];
                @endphp
                <nav class="space-y-5">
                    @foreach($navSections as $sectionLabel => $items)
                        <div class="space-y-2">
                            <p class="px-3 text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $sectionLabel }}</p>
                            <div class="space-y-1">
                                @foreach($items as $item)
                                    @php
                                        $isAvailable = isset($item['route']) && Route::has($item['route']);
                                        $url = $isAvailable ? route($item['route']) : '#';
                                        $active = $isAvailable && request()->routeIs($item['route'] . '*');
                                    @endphp
                                    <a href="{{ $url }}" class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-200 focus-visible:ring-offset-2 {{ $active ? 'bg-gradient-to-r from-purple-50/90 via-white to-emerald-50/90 text-purple-700 shadow-sm ring-1 ring-purple-200/60' : 'text-slate-700 hover:bg-slate-50/70 hover:text-purple-700' }}">
                                        <span class="flex h-9 w-9 items-center justify-center rounded-lg border {{ $active ? 'border-purple-200 bg-white text-purple-600' : 'border-slate-200 bg-white/80 text-slate-500 group-hover:border-purple-200 group-hover:text-purple-600' }} shadow-sm">
                                            {!! $icons[$item['icon']] ?? '' !!}
                                        </span>
                                        <span>{{ $item['label'] }}</span>
                                        @if($active)
                                            <span class="absolute inset-y-1 left-1 w-1 rounded-full bg-gradient-to-b from-purple-400 to-emerald-300"></span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </nav>
            </aside>

            <main class="flex-1">
                <div class="mx-auto max-w-7xl px-6 py-8">
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
