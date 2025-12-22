@props([
    'label',
    'value',
    'hint' => null,
    'icon' => null,
])

<div class="bg-white border border-gray-200 border-t-4 border-t-mint-200 rounded-xl shadow-soft p-5 flex items-start gap-4">
    @if($icon)
        <div class="h-12 w-12 rounded-full bg-mint-50 text-mint-600 flex items-center justify-center">{!! $icon !!}</div>
    @endif
    <div class="flex-1">
        <p class="text-sm text-gray-500">{{ $label }}</p>
        <p class="text-2xl font-semibold text-gray-900 leading-tight">{{ $value }}</p>
        @if($hint)
            <p class="text-sm text-gray-500 mt-1">{{ $hint }}</p>
        @endif
    </div>
</div>
