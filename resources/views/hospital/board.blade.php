@extends('layouts.app')
@section('content')
<div class="container">
    <h1 class="text-xl font-bold mb-4">Tablero hospital</h1>
    <div class="grid grid-cols-2 gap-4">
        @foreach($cages as $cage)
            <div class="border p-2">
                <div class="font-semibold">{{ $cage->name }} ({{ $cage->location }})</div>
                @forelse($cage->stays as $stay)
                    <div class="mt-2">
                        <div>Paciente #{{ $stay->patient_id }} - {{ $stay->severity }}</div>
                        <div class="text-sm text-gray-600">Plan: {{ $stay->plan }}</div>
                    </div>
                @empty
                    <div class="text-sm text-gray-600">Libre</div>
                @endforelse
            </div>
        @endforeach
    </div>
</div>
@endsection
