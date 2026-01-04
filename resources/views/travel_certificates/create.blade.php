@extends('layouts.app')

@push('styles')
<style>
    .gradient-surface {
        background: radial-gradient(circle at top left, #e0f2ff 0%, #f8faff 35%, #ffffff 70%);
    }

    .certificate-overview-bg {
        background: linear-gradient(135deg, #eef2ff 0%, #ffffff 45%, #e0f2fe 100%);
    }

    .primary-action {
        background-color: #2563eb;
        color: #ffffff;
    }

    .primary-action:hover {
        background-color: #1d4ed8;
        color: #ffffff;
    }

    .primary-action:focus {
        outline: 2px solid #bfdbfe;
        outline-offset: 2px;
    }
</style>
@endpush

@section('content')
<div class="-mx-6 -my-8 px-6 py-8 gradient-surface min-h-[calc(100vh-10rem)]">
    <div class="w-full space-y-6">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-semibold text-blue-700 uppercase tracking-wide">Certificados de viaje</p>
                <h1 class="text-3xl font-bold text-gray-900">Nuevo certificado</h1>
                <p class="text-sm text-gray-600 mt-1">Completa la información del tutor, paciente y viaje en una sola vista amplia.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('travel-certificates.index') }}" class="inline-flex items-center px-3 py-2 text-sm font-semibold text-gray-800 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-50 hover:border-gray-300">
                    Volver al listado
                </a>
                <button form="travel-certificate-form" type="submit" class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg shadow-md focus:ring-2 focus:ring-offset-1 focus:ring-blue-500 focus:outline-none transition-colors primary-action">
                    Guardar certificado
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1">
            <div class="bg-white shadow-soft rounded-2xl border border-gray-100 p-6 h-full">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Detalles del certificado</h2>
                <form id="travel-certificate-form" method="POST" action="{{ route('travel-certificates.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @include('travel_certificates._form', ['prefill' => $prefill ?? [], 'patient' => $patient ?? null])
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
