@props([
    'title',
    'chartId',
    'height' => '220',
])

<div class="bg-white border border-gray-200 rounded-xl p-4 shadow-soft">
    <p class="text-sm font-medium text-gray-700 mb-2">{{ $title }}</p>
    <div style="height: {{ $height }}px;">
        <canvas id="{{ $chartId }}" height="{{ $height }}"></canvas>
    </div>
</div>
