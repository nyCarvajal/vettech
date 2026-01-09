@props([
    'headers' => [],
    'rows',
])

<div class="bg-white border border-gray-200 rounded-xl shadow-soft overflow-hidden">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                @foreach($headers as $header)
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($rows as $row)
                <tr>
                    @foreach($row as $value)
                        <td class="px-4 py-3 text-gray-700">{{ $value }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td class="px-4 py-6 text-center text-gray-500" colspan="{{ count($headers) }}">Sin registros</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@if(method_exists($rows, 'links'))
    <div class="mt-4">
        {{ $rows->withQueryString()->links() }}
    </div>
@endif
