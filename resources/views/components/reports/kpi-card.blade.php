@props([
    'label',
    'value',
    'hint' => null,
])

<div class="bg-white border border-gray-200 rounded-xl p-4 shadow-soft">
    <p class="text-sm text-gray-500">{{ $label }}</p>
    <p class="text-2xl font-semibold text-gray-900">{{ $value }}</p>
    @if($hint)
        <p class="text-xs text-gray-400 mt-1">{{ $hint }}</p>
    @endif
</div>
