@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
])

@php
    $base = 'inline-flex items-center justify-center gap-2 font-medium rounded-lg transition focus:outline-none focus:ring-2 focus:ring-offset-2';
    $sizes = [
        'sm' => 'px-3 py-2 text-sm',
        'md' => 'px-4 py-2 text-sm',
    ];
    $variants = [
        'primary' => 'bg-mint-600 text-white hover:bg-mint-700 focus:ring-mint-200',
        'secondary' => 'border border-mint-600 text-mint-700 bg-white hover:bg-mint-50 focus:ring-mint-200',
        'ghost' => 'text-gray-600 hover:bg-gray-50 focus:ring-gray-200',
        'danger' => 'bg-danger-500 text-white hover:bg-red-500 focus:ring-danger-500/40',
    ];
@endphp

@php
    $asLink = $attributes->has('href');
    $tag = $asLink ? 'a' : 'button';
@endphp

<{{ $tag }} @unless($asLink) type="{{ $type }}" @endunless {{ $attributes->merge(['class' => $base . ' ' . ($sizes[$size] ?? $sizes['md']) . ' ' . ($variants[$variant] ?? $variants['primary'])]) }}>
    {{ $slot }}
</{{ $tag }}>
