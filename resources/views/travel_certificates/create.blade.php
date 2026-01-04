@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6 space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-slate-500">Certificados de viaje</p>
            <h1 class="text-3xl font-semibold text-slate-900">Nuevo certificado</h1>
        </div>
        @if($patient ?? false)
            <span class="px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-sm font-semibold">Desde paciente #{{ $patient->id }}</span>
        @endif
    </div>
    <form method="POST" action="{{ route('travel-certificates.store') }}" enctype="multipart/form-data">
        @include('travel_certificates._form', ['prefill' => $prefill ?? [], 'patient' => $patient ?? null])
    </form>
</div>
@endsection
