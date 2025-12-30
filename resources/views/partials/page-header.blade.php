@props([
    'title',
    'subtitle' => null,
])

<div class="relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white/80 p-5 shadow-soft backdrop-blur">
    <div class="absolute inset-0 bg-gradient-to-r from-purple-50/60 via-white to-emerald-50/60 opacity-80 pointer-events-none"></div>
    <div class="relative flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="space-y-1">
            <h1 class="text-2xl font-semibold text-gray-900">{{ $title }}</h1>
            @if($subtitle)
                <p class="text-gray-600 text-sm">{{ $subtitle }}</p>
            @endif
        </div>
        @if(isset($actions))
            <div class="flex flex-wrap gap-2">{!! $actions !!}</div>
        @endif
    </div>
</div>
