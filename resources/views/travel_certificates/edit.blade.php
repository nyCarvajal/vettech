@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6 space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-slate-500">Certificados de viaje</p>
            <h1 class="text-3xl font-semibold text-slate-900">Editar {{ $certificate->code }}</h1>
        </div>
    </div>
    <form method="POST" action="{{ route('travel-certificates.update', $certificate) }}" enctype="multipart/form-data">
        @method('PUT')
        @include('travel_certificates._form', ['certificate' => $certificate, 'prefill' => []])
    </form>
</div>
@endsection
