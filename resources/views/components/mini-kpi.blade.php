@props([
    'label',
    'value' => '--',
    'icon' => null,
    'accent' => 'mint',
    'hint' => null,
])

@php
    $accentClasses = [
        'mint' => ['bg' => 'from-emerald-50 to-white', 'icon' => 'bg-emerald-100 text-emerald-600'],
        'purple' => ['bg' => 'from-purple-50 to-white', 'icon' => 'bg-purple-100 text-purple-600'],
        'blue' => ['bg' => 'from-sky-50 to-white', 'icon' => 'bg-sky-100 text-sky-600'],
        'amber' => ['bg' => 'from-amber-50 to-white', 'icon' => 'bg-amber-100 text-amber-600'],
    ];
    $tone = $accentClasses[$accent] ?? $accentClasses['mint'];
@endphp

<div class="relative overflow-hidden rounded-2xl border border-slate-200/80 bg-gradient-to-br {{ $tone['bg'] }} p-4 shadow-sm">
    <div class="flex items-center gap-3">
        <div class="flex h-11 w-11 items-center justify-center rounded-xl {{ $tone['icon'] }} shadow-inner shadow-white/60">
            {!! $icon !!}
        </div>
        <div class="flex-1">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $label }}</p>
            <p class="text-3xl font-semibold text-slate-900 leading-tight">{{ $value }}</p>
            @if($hint)
                <p class="text-xs text-slate-500 mt-0.5">{{ $hint }}</p>
            @endif
        </div>
    </div>
</div>
