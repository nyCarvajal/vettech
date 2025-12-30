@props([
    'title' => 'Sin datos',
    'description' => 'Aún no hay información para mostrar',
])

<div class="text-center py-10 px-4">
    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-mint-50 text-mint-600">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6">
            <path d="M5.636 5.636a9 9 0 1112.728 12.728A9 9 0 015.636 5.636zm9.192 2.121a1 1 0 00-1.415 0L12 9.172l-1.414-1.415a1 1 0 00-1.415 1.415L10.586 10.5l-1.415 1.415a1 1 0 101.415 1.414L12 11.914l1.414 1.415a1 1 0 001.415-1.414L13.414 10.5l1.415-1.414a1 1 0 000-1.329z" />
        </svg>
    </div>
    <h3 class="mt-4 text-lg font-semibold text-gray-900">{{ $title }}</h3>
    <p class="mt-1 text-sm text-gray-500">{{ $description }}</p>
    @if($slot->isNotEmpty())
        <div class="mt-4 flex justify-center">{{ $slot }}</div>
    @endif
</div>
