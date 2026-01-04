@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6 space-y-4">
    <div class="bg-white shadow rounded p-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold">{{ $procedure->name }} ({{ $procedure->code }})</h1>
            <p class="text-sm text-gray-600">Paciente: {{ $procedure->patient_snapshot['name'] ?? 'N/A' }} | Estado: {{ str_replace('_', ' ', $procedure->status) }}</p>
        </div>
        <form method="POST" action="{{ route('procedures.change-status', $procedure) }}" class="flex space-x-2">
            @csrf
            <select name="status" class="input input-bordered">
                @foreach(['scheduled'=>'Programado','in_progress'=>'En curso','completed'=>'Completado','canceled'=>'Cancelado'] as $value=>$label)
                <option value="{{ $value }}" @selected($procedure->status===$value)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="bg-indigo-600 text-white px-3 py-2 rounded">Cambiar</button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded shadow p-4 space-y-2">
            <h2 class="font-semibold">Notas</h2>
            <p><strong>Pre:</strong> {{ $procedure->preop_notes }}</p>
            <p><strong>Intra:</strong> {{ $procedure->intraop_notes }}</p>
            <p><strong>Post:</strong> {{ $procedure->postop_notes }}</p>
            <p><strong>Complicaciones:</strong> {{ $procedure->complications }}</p>
        </div>
        <div class="bg-white rounded shadow p-4 space-y-2">
            <h2 class="font-semibold">Anestesia</h2>
            <p><strong>Plan:</strong> {{ $procedure->anesthesia_plan }}</p>
            <p><strong>Monitoreo:</strong> {{ $procedure->anesthesia_monitoring }}</p>
            <p><strong>Notas:</strong> {{ $procedure->anesthesia_notes }}</p>
            <div>
                <h3 class="font-medium">Medicamentos</h3>
                <ul class="text-sm list-disc pl-4">
                    @forelse($procedure->anesthesiaMedications as $med)
                    <li>{{ $med->drug_name }} ({{ $med->dose }} {{ $med->dose_unit }}) {{ $med->route }} {{ $med->frequency }}</li>
                    @empty
                    <li class="text-gray-500">Sin datos</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <div class="bg-white rounded shadow p-4">
        <h2 class="font-semibold mb-2">Consentimiento</h2>
        @if($procedure->consent_document_id)
            <p class="text-sm">Documento vinculado ID: {{ $procedure->consent_document_id }}</p>
        @else
            <p class="text-sm text-gray-500">Sin consentimiento asociado.</p>
        @endif
        <form method="POST" action="{{ route('procedures.consent.link', $procedure) }}" class="mt-2 flex space-x-2">
            @csrf
            <input type="text" name="consent_document_id" class="input input-bordered" placeholder="ID firmado">
            <button class="bg-gray-800 text-white px-3 py-2 rounded">Vincular firmado</button>
        </form>
        <form method="POST" action="{{ route('procedures.consent.create', $procedure) }}" class="mt-2 flex space-x-2">
            @csrf
            <input type="number" name="template_id" class="input input-bordered" placeholder="Plantilla">
            <button class="bg-indigo-600 text-white px-3 py-2 rounded">Generar desde plantilla</button>
        </form>
    </div>

    <div class="bg-white rounded shadow p-4">
        <div class="flex items-center justify-between mb-2">
            <h2 class="font-semibold">Adjuntos</h2>
            <form method="POST" action="{{ route('procedures.attachments.store', $procedure) }}" enctype="multipart/form-data" class="flex space-x-2 items-center">
                @csrf
                <input type="text" name="title" class="input input-bordered" placeholder="Título" required>
                <input type="file" name="file" required>
                <button class="bg-indigo-600 text-white px-3 py-2 rounded">Subir</button>
            </form>
        </div>
        <ul class="text-sm divide-y">
            @forelse($procedure->attachments as $attachment)
            <li class="py-2 flex items-center justify-between">
                <span>{{ $attachment->title }} ({{ $attachment->mime }})</span>
                <form method="POST" action="{{ route('procedures.attachments.destroy', [$procedure, $attachment]) }}">
                    @csrf
                    @method('DELETE')
                    <button class="text-red-600">Eliminar</button>
                </form>
            </li>
            @empty
            <li class="py-2 text-gray-500">Sin adjuntos</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
