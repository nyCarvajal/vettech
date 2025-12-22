@props([
    'type' => 'info',
    'title' => null,
])

@php
    $colors = [
        'success' => 'border-mint-200 bg-mint-50 text-mint-700',
        'error' => 'border-danger-500/40 bg-red-50 text-danger-500',
        'info' => 'border-gray-200 bg-gray-50 text-gray-700',
        'warning' => 'border-warning-500/40 bg-amber-50 text-warning-500',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'rounded-lg border-l-4 p-4 text-sm ' . ($colors[$type] ?? $colors['info'])]) }}>
    @if($title)
        <p class="font-semibold mb-1">{{ $title }}</p>
    @endif
    <div class="space-y-1">{{ $slot }}</div>
</div>
