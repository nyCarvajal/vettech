@props([
    'actions' => [],
])

<div class="flex flex-wrap gap-2">
    @foreach($actions as $action)
        <x-button :variant="$action['variant'] ?? 'primary'" size="sm" :href="$action['route'] ?? '#'"><span>{{ $action['label'] }}</span></x-button>
    @endforeach
</div>
