@props([
    'title' => 'No hay datos aún',
    'description' => 'Hoy está tranquilo 🐾. Registra una acción para ver actividad aquí.',
])

<div class="text-center py-10 px-4">
    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-purple-50 via-white to-emerald-50 text-purple-600 shadow-inner shadow-purple-100">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-8 w-8">
            <path d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-.53 5.72a.75.75 0 011.06 0l2.25 2.25a.75.75 0 11-1.06 1.06L12.75 10.81v5.44a.75.75 0 11-1.5 0v-5.44L10.28 11.28a.75.75 0 11-1.06-1.06l2.25-2.25z" />
        </svg>
    </div>
    <h3 class="mt-4 text-lg font-semibold text-gray-900">{{ $title }}</h3>
    <p class="mt-1 text-sm text-gray-500">{{ $description }}</p>
    @if($slot->isNotEmpty())
        <div class="mt-5 flex flex-wrap items-center justify-center gap-2">{{ $slot }}</div>
    @endif
</div>
