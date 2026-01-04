@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-6 space-y-4">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold">Certificado {{ $certificate->code }}</h1>
        <div class="space-x-2">
            <a href="{{ route('travel-certificates.pdf', $certificate) }}" class="bg-gray-200 px-3 py-1 rounded">PDF</a>
            <form class="inline" method="POST" action="{{ route('travel-certificates.duplicate', $certificate) }}">@csrf<button class="bg-gray-200 px-3 py-1 rounded">Duplicar</button></form>
        </div>
    </div>
    <div class="bg-white shadow rounded p-4">
        <p><strong>Tipo:</strong> {{ $certificate->type }}</p>
        <p><strong>Estado:</strong> {{ $certificate->status }}</p>
        <p><strong>Mascota:</strong> {{ $certificate->pet_name }} ({{ $certificate->pet_species }})</p>
        <p><strong>Tutor:</strong> {{ $certificate->owner_name }}</p>
        <p><strong>Viaje:</strong> {{ $certificate->travel_departure_date?->format('Y-m-d') }}</p>
    </div>
    <div class="bg-white shadow rounded p-4">
        <h2 class="text-lg font-semibold mb-2">Vacunas</h2>
        <ul class="list-disc pl-5">
            @forelse($certificate->vaccinations as $vaccination)
                <li>{{ $vaccination->vaccine_name }} - {{ $vaccination->applied_at?->format('Y-m-d') }}</li>
            @empty
                <li>Sin registros</li>
            @endforelse
        </ul>
    </div>
    <div class="bg-white shadow rounded p-4">
        <h2 class="text-lg font-semibold mb-2">Desparasitaciones</h2>
        <ul class="list-disc pl-5">
            @forelse($certificate->dewormings as $deworming)
                <li>{{ ucfirst($deworming->kind) }} - {{ $deworming->product_name }} ({{ $deworming->applied_at?->format('Y-m-d') }})</li>
            @empty
                <li>Sin registros</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
