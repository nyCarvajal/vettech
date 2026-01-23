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
    <script>
        window.tailwind = window.tailwind || {};
        window.tailwind.config = {
            corePlugins: {
                preflight: false,
            },
            theme: {
                extend: {
                    colors: {
                        mint: {
                            50: 'var(--mint-50)',
                            100: 'var(--mint-100)',
                            200: 'var(--mint-200)',
                            500: 'var(--mint-500)',
                            600: 'var(--mint-600)',
                            700: 'var(--mint-700)',
                        },
                        gray: {
                            50: 'var(--gray-50)',
                            100: 'var(--gray-100)',
                            200: 'var(--gray-200)',
                            500: 'var(--gray-500)',
                            700: 'var(--gray-700)',
                        },
                        danger: {
                            500: 'var(--danger-500)',
                        },
                        warning: {
                            500: 'var(--warning-500)',
                        },
                    },
                    fontFamily: {
                        sans: ['var(--font-sans)', 'Inter', 'Nunito', 'ui-sans-serif', 'system-ui', '-apple-system', 'Segoe UI', 'sans-serif'],
                    },
                },
            },
        };
    </script>
    @if($hasViteAssets)
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/scss/icons.scss', 'resources/scss/style.scss', 'resources/sass/app.scss'])
    @else
        <!-- Fallback CSS y JS para cuando Vite no está disponible -->
        <link rel="stylesheet" href="{{ asset('css/app-fallback.css') }}">
        <script id="tailwind-cdn-primary" src="https://cdn.tailwindcss.com"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    @endif

    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- Fallback CSS para cuando Vite no está disponible o el bundle no carga -->
    <link rel="stylesheet" href="{{ asset('css/app-fallback.css') }}">
    <noscript>
        <link rel="stylesheet" href="{{ asset('css/app-fallback.css') }}">
    </noscript>
    <script>
        function ensureFallbackStyles() {
            const ensureFallbackCss = () => {
                const alreadyLoaded = Array.from(document.querySelectorAll('link[rel="stylesheet"]'))
                    .some((link) => link.getAttribute('href')?.includes('app-fallback.css'));
                if (!alreadyLoaded) {
                    const fallbackLink = document.createElement('link');
                    fallbackLink.rel = 'stylesheet';
                    fallbackLink.href = '{{ asset('css/app-fallback.css') }}';
                    document.head.appendChild(fallbackLink);
                }
            };

            const loadTailwindCdn = () => {
                if (document.getElementById('tailwind-cdn-primary') || document.getElementById('tailwind-cdn-fallback')) {
                    return;
                }
                const script = document.createElement('script');
                script.id = 'tailwind-cdn-fallback';
                script.src = 'https://cdn.tailwindcss.com';
                document.head.appendChild(script);
            };

            const mint600 = getComputedStyle(document.documentElement).getPropertyValue('--mint-600');
            if (!mint600 || mint600.trim().length === 0) {
                ensureFallbackCss();
            }

            const probe = document.createElement('div');
            probe.className = 'bg-mint-600 text-white px-2 py-1 rounded';
            probe.style.position = 'absolute';
            probe.style.opacity = '0';
            document.body.appendChild(probe);

            const bgColor = getComputedStyle(probe).backgroundColor;
            const hasBackground = bgColor && bgColor !== 'rgba(0, 0, 0, 0)' && bgColor !== 'transparent';
            probe.remove();

            if (!hasBackground) {
                ensureFallbackCss();
                loadTailwindCdn();
            }
        }

        window.ensureFallbackStyles = ensureFallbackStyles;

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', ensureFallbackStyles, { once: true });
        } else {
            ensureFallbackStyles();
        }

        window.addEventListener('load', ensureFallbackStyles, { once: true });
    </script>

    @stack('styles')
</head>
<body class="bg-gray-50">
    <div id="app" class="min-h-screen flex flex-col">
        <header class="bg-white border-b border-gray-200">
            <div class="h-16 px-6 flex items-center justify-between gap-6">
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center" aria-label="Ir al dashboard">
                        <img
                            src="{{ asset('images/logo-dark.png') }}"
                            alt="{{ config('app.name', 'VetTech') }}"
                            class="h-48 w-auto"
                        />
                    </a>
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
                    $featureDefaults = \App\Models\Clinica::featureDefaults();
                    $featureEnabled = function (string $key) use ($clinica, $featureDefaults): bool {
                        if ($clinica) {
                            return $clinica->featureEnabled($key, $featureDefaults[$key] ?? true);
                        }

                        return $featureDefaults[$key] ?? true;
                    };

                    $navSections = [
                        [
                            'title' => 'OPERACIÓN',
                            'items' => array_filter([
                                ['label' => 'Dashboard', 'route' => 'dashboard'],
                                ['label' => 'Agenda', 'route' => 'agenda.index'],
                                ['label' => 'Facturación POS', 'route' => 'invoices.pos'],
                                ['label' => 'Tutores', 'route' => 'owners.index'],
                                ['label' => 'Pacientes', 'route' => 'patients.index'],
                            ]),
                        ],
                        [
                            'title' => 'SERVICIOS / CLÍNICA',
                            'items' => array_filter([
                                ['label' => 'Dispensación', 'route' => 'dispensations.index'],
                                ['label' => 'Hospitalización 24/7', 'route' => 'hospital.index'],
                                ['label' => 'Sala de belleza', 'route' => 'groomings.index'],
                                ['label' => 'Consentimientos', 'route' => 'consents.index'],
                                ['label' => 'Plantillas de consentimientos', 'route' => 'consent-templates.index'],
                            ]),
                        ],
                        [
                            'title' => 'CAJA Y REPORTES',
                            'items' => array_filter([
                                ['label' => 'Arqueo de caja', 'route' => 'cash.closures.create'],
                                [
                                    'label' => 'Reportes básicos',
                                    'route' => 'reports.quick',
                                    'active' => 'reports.quick*',
                                ],
                                [
                                    'label' => 'Reportes avanzados',
                                    'route' => 'reports.home',
                                    'active' => 'reports.*',
                                ],
                            ]),

                        ],
                        [
                            'title' => 'CONFIGURACIÓN',
                            'items' => array_filter([
                                ['label' => 'Catálogos', 'route' => 'configuracion.index'],
                                $featureEnabled('config_clinica') ? ['label' => 'Configuración de clínicas', 'route' => 'settings.clinica.edit'] : null,
                                $featureEnabled('plantillas_consentimientos') ? ['label' => 'Plantillas de consentimientos', 'route' => 'consent-templates.index'] : null,
         ]),
                        ],
                    ];

                    $navSections = array_filter($navSections, fn ($section) => count($section['items']) > 0);
                @endphp
                <nav class="p-4 space-y-4">
                    @foreach($navSections as $section)
                        <div class="space-y-1">
                            <p class="px-3 text-xs font-semibold uppercase tracking-wide text-gray-400">
                                {{ $section['title'] }}
                            </p>
                            @foreach($section['items'] as $item)
                                @php
                                    $isAvailable = isset($item['route']) && Route::has($item['route']);
                                    $url = $isAvailable ? route($item['route']) : '#';
                                    $activePattern = $item['active'] ?? ($item['route'] . '*');
                                    $active = $isAvailable && request()->routeIs($activePattern);
                                @endphp
                                <a href="{{ $url }}" class="sidebar-link {{ $active ? 'sidebar-link-active' : '' }}">
                                    <span>{{ $item['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
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
