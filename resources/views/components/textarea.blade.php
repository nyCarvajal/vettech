@props([
    'label' => null,
    'name' => null,
    'id' => null,
    'rows' => 4,
    'help' => null,
    'error' => null,
])

@php
    $textareaId = $id ?? $name ?? uniqid('textarea-');
@endphp

<div class="space-y-1">
    @if($label)
        <label for="{{ $textareaId }}" class="text-sm font-medium text-gray-700">{{ $label }}</label>
    @endif
    <textarea id="{{ $textareaId }}" name="{{ $name }}" rows="{{ $rows }}" {{ $attributes->merge(['class' => 'block w-full rounded-lg border-gray-200 focus:border-mint-500 focus:ring-2 focus:ring-mint-200 text-sm']) }}>{{ $slot }}</textarea>
    @if($help)
        <p class="text-xs text-gray-500">{{ $help }}</p>
    @endif
    @if($error)
        <p class="text-xs text-danger-500">{{ $error }}</p>
    @endif
</div>
