@props([
    'title' => null,
    'subtitle' => null,
    'actions' => null,
])

<div {{ $attributes->class('relative overflow-hidden bg-white/90 backdrop-blur-sm border border-slate-200 rounded-2xl shadow-soft p-5') }}>
    @if($title || $subtitle || $actions)
        <div class="flex items-start justify-between gap-4 mb-4">
            <div class="space-y-1">
                @if($title)
                    <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
                @endif
                @if($subtitle)
                    <p class="text-sm text-gray-500">{{ $subtitle }}</p>
                @endif
            </div>
            @if($actions)
                <div class="flex items-center gap-2 shrink-0">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif
    <div class="text-gray-700">
        {{ $slot }}
    </div>
</div>
