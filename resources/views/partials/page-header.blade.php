@props([
    'title',
    'subtitle' => null,
])

<div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
    <div class="space-y-1">
        <h1 class="text-2xl font-semibold text-gray-900">{{ $title }}</h1>
        @if($subtitle)
            <p class="text-gray-600 text-sm">{{ $subtitle }}</p>
        @endif
    </div>
    @if(isset($actions))
        <div class="flex flex-wrap gap-2">{{ $actions }}</div>
    @endif
</div>
