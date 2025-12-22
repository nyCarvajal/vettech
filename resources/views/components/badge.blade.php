@props([
    'variant' => 'mint',
    'text' => '',
])

@php
    $styles = [
        'mint' => 'bg-mint-50 text-mint-700 ring-1 ring-inset ring-mint-200',
        'gray' => 'bg-gray-100 text-gray-700 ring-1 ring-inset ring-gray-200',
        'danger' => 'bg-red-50 text-danger-500 ring-1 ring-inset ring-danger-500/40',
        'warning' => 'bg-amber-50 text-warning-500 ring-1 ring-inset ring-warning-500/40',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium ' . ($styles[$variant] ?? $styles['mint'])]) }}>
    {{ $text }}
</span>
