@props([
    'label' => null,
    'name' => null,
    'id' => null,
    'type' => 'text',
    'help' => null,
    'error' => null,
])

@php
    $inputId = $id ?? $name ?? uniqid('input-');
@endphp

<div class="space-y-1">
    @if($label)
        <label for="{{ $inputId }}" class="text-sm font-medium text-gray-700">{{ $label }}</label>
    @endif
    <input id="{{ $inputId }}" name="{{ $name }}" type="{{ $type }}" {{ $attributes->merge(['class' => 'block w-full rounded-lg border-gray-200 focus:border-mint-500 focus:ring-2 focus:ring-mint-200 text-sm']) }} />
    @if($help)
        <p class="text-xs text-gray-500">{{ $help }}</p>
    @endif
    @if($error)
        <p class="text-xs text-danger-500">{{ $error }}</p>
    @endif
</div>
