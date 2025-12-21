@extends('layouts.app')
@section('content')
<div class="container">
    <h1 class="text-xl font-bold mb-4">Hospitalizaciones</h1>
    <a href="{{ route('hospital.stays.create') }}" class="btn btn-primary">Nueva admisión</a>
    <ul class="mt-4 space-y-2">
        @foreach($stays as $stay)
            <li class="border p-2">
                Paciente #{{ $stay->patient_id }} en {{ $stay->cage->name }} - {{ $stay->status }}
            </li>
        @endforeach
    </ul>
    {{ $stays->links() }}
</div>
@endsection
