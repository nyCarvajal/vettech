@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-emerald-700">Hospitalización</h1>
        <x-button href="{{ route('hospital.admit') }}">Admitir paciente</x-button>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($stays as $stay)
            <x-card class="bg-white border border-emerald-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">{{ $stay->admitted_at->format('d M Y') }}</p>
                        <h3 class="text-lg font-semibold text-emerald-700">{{ $stay->patient->name ?? 'Paciente' }}</h3>
                        <p class="text-sm text-gray-600">{{ $stay->owner->name ?? '' }}</p>
                        <p class="text-sm text-gray-500">Día {{ $stay->daysSinceAdmission() }} • Severidad: <x-badge>{{ ucfirst($stay->severity) }}</x-badge></p>
                    </div>
                    <div class="text-right space-y-2">
                        <x-badge>{{ $stay->status === 'active' ? 'Hospitalizado' : 'Alta' }}</x-badge>
                        <x-button href="{{ route('hospital.show', $stay) }}" size="sm">Abrir</x-button>
                        <form method="POST" action="{{ route('hospital.discharge', $stay) }}">
                            @csrf
                            <x-button type="submit" size="sm" color="danger">Alta</x-button>
                        </form>
                    </div>
                </div>
            </x-card>
        @endforeach
    </div>
</div>
@endsection
