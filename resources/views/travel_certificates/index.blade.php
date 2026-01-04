@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">Certificados de Viaje</h1>
        <a href="{{ route('travel-certificates.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">Nuevo</a>
    </div>

    <table class="min-w-full bg-white shadow rounded">
        <thead>
            <tr class="text-left">
                <th class="p-2">Código</th>
                <th class="p-2">Tipo</th>
                <th class="p-2">Mascota</th>
                <th class="p-2">Tutor</th>
                <th class="p-2">Fecha viaje</th>
                <th class="p-2">Estado</th>
                <th class="p-2"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($certificates as $certificate)
                <tr class="border-t">
                    <td class="p-2">{{ $certificate->code }}</td>
                    <td class="p-2">{{ $certificate->type }}</td>
                    <td class="p-2">{{ $certificate->pet_name }}</td>
                    <td class="p-2">{{ $certificate->owner_name }}</td>
                    <td class="p-2">{{ $certificate->travel_departure_date?->format('Y-m-d') }}</td>
                    <td class="p-2">{{ $certificate->status }}</td>
                    <td class="p-2 space-x-2">
                        <a class="text-blue-600" href="{{ route('travel-certificates.show', $certificate) }}">Ver</a>
                        <a class="text-blue-600" href="{{ route('travel-certificates.edit', $certificate) }}">Editar</a>
                    </td>
                </tr>
            @empty
                <tr><td class="p-4" colspan="7">Sin registros</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">{{ $certificates->links() }}</div>
</div>
@endsection
