@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
])

@php
    $base = 'inline-flex items-center justify-center gap-2 font-medium rounded-lg transition focus:outline-none focus:ring-2 focus:ring-offset-2 shadow-sm';
    $sizes = [
        'sm' => 'px-3 py-2 text-sm',
        'md' => 'px-4 py-2 text-sm',
    ];
    $variants = [
        'primary' => 'text-white bg-gradient-to-r from-purple-500 via-purple-400 to-emerald-400 hover:from-purple-400 hover:via-purple-300 hover:to-emerald-300 shadow-md shadow-purple-200/60 focus:ring-emerald-200',
        'secondary' => 'bg-white text-purple-700 ring-1 ring-purple-200 hover:bg-gradient-to-r hover:from-purple-50 hover:via-white hover:to-emerald-50 focus:ring-purple-200',
        'ghost' => 'text-purple-700 hover:bg-gradient-to-r hover:from-purple-50 hover:via-white hover:to-emerald-50 focus:ring-purple-200',
        'danger' => 'text-white bg-gradient-to-r from-rose-500 via-rose-400 to-amber-400 hover:brightness-110 shadow-md shadow-rose-200/50 focus:ring-rose-200/70',
    ];
@endphp

@php
    $asLink = $attributes->has('href');
    $tag = $asLink ? 'a' : 'button';
@endphp

<{{ $tag }} @unless($asLink) type="{{ $type }}" @endunless {{ $attributes->merge(['class' => $base . ' ' . ($sizes[$size] ?? $sizes['md']) . ' ' . ($variants[$variant] ?? $variants['primary'])]) }}>
    {{ $slot }}
</{{ $tag }}>
