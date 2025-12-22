@props([
    'label' => null,
    'name' => null,
    'id' => null,
    'help' => null,
    'error' => null,
])

@php
    $selectId = $id ?? $name ?? uniqid('select-');
@endphp

<div class="space-y-1">
    @if($label)
        <label for="{{ $selectId }}" class="text-sm font-medium text-gray-700">{{ $label }}</label>
    @endif
    <select id="{{ $selectId }}" name="{{ $name }}" {{ $attributes->merge(['class' => 'block w-full rounded-lg border-gray-200 focus:border-mint-500 focus:ring-2 focus:ring-mint-200 text-sm bg-white']) }}>
        {{ $slot }}
    </select>
    @if($help)
        <p class="text-xs text-gray-500">{{ $help }}</p>
    @endif
    @if($error)
        <p class="text-xs text-danger-500">{{ $error }}</p>
    @endif
</div>
