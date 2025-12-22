@props([
    'headers' => [],
    'empty' => false,
    'emptyMessage' => 'Sin registros disponibles',
])

<div class="overflow-hidden border border-gray-200 rounded-xl bg-white shadow-soft">
    <table class="min-w-full divide-y divide-gray-200">
        @if(!empty($headers))
            <thead class="bg-gray-50 text-left text-sm text-gray-500">
                <tr>
                    @foreach($headers as $header)
                        <th scope="col" class="px-4 py-3 font-medium">{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
        @endif
        <tbody class="divide-y divide-gray-100 text-sm text-gray-700 [&>tr:hover]:bg-gray-50">
            @if($empty)
                <tr>
                    <td colspan="{{ count($headers) ?: 1 }}" class="px-4 py-6 text-center text-gray-500">{{ $emptyMessage }}</td>
                </tr>
            @else
                {{ $slot }}
            @endif
        </tbody>
    </table>
</div>
