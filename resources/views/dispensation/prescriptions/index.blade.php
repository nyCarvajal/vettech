@extends('layouts.app')
@section('content')
<div class="container">
    <h1 class="text-xl font-bold mb-4">Fórmulas</h1>
    <a href="{{ route('prescriptions.create') }}" class="btn btn-primary">Nueva fórmula</a>
    <ul class="mt-4 space-y-2">
        @foreach($prescriptions as $prescription)
            <li class="border p-2">
                Paciente #{{ $prescription->patient_id }} - Estado: {{ $prescription->status }}
            </li>
        @endforeach
    </ul>
    {{ $prescriptions->links() }}
</div>
@endsection
