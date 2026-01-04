@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Editar Certificado {{ $certificate->code }}</h1>
    <form method="POST" action="{{ route('travel-certificates.update', $certificate) }}" enctype="multipart/form-data">
        @method('PUT')
        @include('travel_certificates._form', ['certificate' => $certificate, 'prefill' => []])
        <div class="mt-4 flex space-x-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Guardar</button>
        </div>
    </form>
</div>
@endsection
