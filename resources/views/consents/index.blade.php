@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Consentimientos</h1>
        <a href="{{ route('consents.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded">Nuevo</a>
    </div>
    <div class="bg-white shadow rounded">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left">Código</th>
                    <th class="px-4 py-2">Plantilla</th>
                    <th class="px-4 py-2">Estado</th>
                    <th class="px-4 py-2">Firmado</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($consents as $consent)
                <tr>
                    <td class="px-4 py-2">{{ $consent->code }}</td>
                    <td class="px-4 py-2">{{ $consent->template->name ?? '' }}</td>
                    <td class="px-4 py-2">{{ $consent->status }}</td>
                    <td class="px-4 py-2">{{ optional($consent->signed_at)->format('Y-m-d H:i') }}</td>
                    <td class="px-4 py-2 text-right">
                        <a href="{{ route('consents.show', $consent) }}" class="text-indigo-600">Ver</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">
            {{ $consents->links() }}
        </div>
    </div>
</div>
@endsection
