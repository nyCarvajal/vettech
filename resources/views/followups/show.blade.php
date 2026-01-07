@extends('layouts.app')

@section('content')
@php use Illuminate\Support\Facades\Storage; @endphp
<div class="space-y-6">
    <div class="rounded-2xl bg-gradient-to-r from-mint-500 to-emerald-500 p-6 text-white flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-white/80">Control</p>
            <h1 class="text-3xl font-bold">{{ $followup->code }}</h1>
            <p class="text-white/80">{{ optional($followup->followup_at)->format('d/m/Y H:i') }} · {{ $followup->patient->display_name ?? 'Paciente no asignado' }}</p>
        </div>
        <div class="flex flex-wrap gap-2 text-sm">
            <a href="{{ route('followups.edit', $followup) }}" class="pill-action bg-white/20 text-white border-white/40">Editar</a>
            <form method="post" action="{{ route('followups.destroy', $followup) }}" onsubmit="return confirm('¿Eliminar control?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="pill-action bg-white/20 text-white border-white/40">Eliminar</button>
            </form>
            <a href="{{ route('followups.create', ['patient_id' => $followup->patient_id]) }}" class="pill-action bg-white/20 text-white border-white/40">Nuevo control</a>
            @if($followup->patient)
                <a href="{{ route('patients.show', $followup->patient) }}" class="pill-action bg-white/20 text-white border-white/40">Volver al paciente</a>
            @endif
        </div>
    </div>

    @if(session('status'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">{{ session('status') }}</div>
    @endif

    <div class="grid gap-4 md:grid-cols-3">
        <div class="md:col-span-2 space-y-4">
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-mint-700">Evolución</p>
                        <h2 class="text-lg font-semibold text-gray-900">Detalle clínico</h2>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-mint-50 px-3 py-1 text-sm font-semibold text-mint-700 capitalize">{{ $followup->improved_status }}</span>
                </div>
                @if($followup->improved_score !== null)
                    <p class="text-sm text-gray-600">Nivel reportado: <span class="font-semibold text-gray-900">{{ $followup->improved_score }}/10</span></p>
                @endif
                <div class="grid gap-3 md:grid-cols-2 mt-4">
                    <div>
                        <p class="text-xs uppercase text-gray-500">Observaciones</p>
                        <p class="text-gray-800 whitespace-pre-line">{{ $followup->observations ?: 'Sin observaciones' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-gray-500">Plan / recomendaciones</p>
                        <p class="text-gray-800 whitespace-pre-line">{{ $followup->plan ?: 'Sin plan adicional' }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3 mt-4">
                    <div>
                        <p class="text-xs uppercase text-gray-500">Responsable</p>
                        <p class="text-gray-900 font-semibold">{{ $followup->performed_by ?? 'No asignado' }}</p>
                        <p class="text-gray-500 text-sm">{{ $followup->performed_by_license }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-gray-500">Próximo control</p>
                        <p class="text-gray-900 font-semibold">{{ optional($followup->next_followup_at)->format('d/m/Y H:i') ?? 'No programado' }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-mint-700">Signos vitales</p>
                        <h2 class="text-lg font-semibold text-gray-900">Monitoreo</h2>
                    </div>
                </div>
                <div class="grid md:grid-cols-3 gap-3 text-sm">
                    <div class="rounded-xl border border-gray-100 p-3">
                        <p class="text-gray-500 text-xs">Temperatura</p>
                        <p class="text-gray-900 font-semibold">{{ $followup->vitals?->temperature_c ? $followup->vitals->temperature_c . ' °C' : '—' }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-100 p-3">
                        <p class="text-gray-500 text-xs">FC</p>
                        <p class="text-gray-900 font-semibold">{{ $followup->vitals?->heart_rate_bpm ? $followup->vitals->heart_rate_bpm . ' lpm' : '—' }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-100 p-3">
                        <p class="text-gray-500 text-xs">FR</p>
                        <p class="text-gray-900 font-semibold">{{ $followup->vitals?->respiratory_rate_rpm ? $followup->vitals->respiratory_rate_rpm . ' rpm' : '—' }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-100 p-3">
                        <p class="text-gray-500 text-xs">Peso</p>
                        <p class="text-gray-900 font-semibold">{{ $followup->vitals?->weight_kg ? $followup->vitals->weight_kg . ' kg' : '—' }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-100 p-3">
                        <p class="text-gray-500 text-xs">Hidratación</p>
                        <p class="text-gray-900 font-semibold capitalize">{{ $followup->vitals?->hydration ?? '—' }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-100 p-3">
                        <p class="text-gray-500 text-xs">Mucosas</p>
                        <p class="text-gray-900 font-semibold capitalize">{{ $followup->vitals?->mucous_membranes ?? '—' }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-100 p-3">
                        <p class="text-gray-500 text-xs">TRC</p>
                        <p class="text-gray-900 font-semibold">{{ $followup->vitals?->capillary_refill_time_sec ?? '—' }} seg</p>
                    </div>
                    <div class="rounded-xl border border-gray-100 p-3">
                        <p class="text-gray-500 text-xs">Dolor</p>
                        <p class="text-gray-900 font-semibold">{{ $followup->vitals?->pain_score_0_10 ?? '—' }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-100 p-3">
                        <p class="text-gray-500 text-xs">PA</p>
                        <p class="text-gray-900 font-semibold">{{ $followup->vitals?->blood_pressure_sys ? $followup->vitals->blood_pressure_sys . '/' . $followup->vitals->blood_pressure_dia . ' (MAP ' . $followup->vitals->blood_pressure_map . ')' : '—' }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-100 p-3">
                        <p class="text-gray-500 text-xs">SpO₂</p>
                        <p class="text-gray-900 font-semibold">{{ $followup->vitals?->o2_saturation_percent ? $followup->vitals->o2_saturation_percent . '%' : '—' }}</p>
                    </div>
                    <div class="md:col-span-3">
                        <p class="text-xs uppercase text-gray-500">Notas</p>
                        <p class="text-gray-800 whitespace-pre-line">{{ $followup->vitals?->notes ?? 'Sin notas adicionales' }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="space-y-4">
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-mint-700">Paciente</p>
                <h3 class="text-lg font-semibold text-gray-900">{{ $followup->patient->display_name ?? 'N/D' }}</h3>
                <p class="text-sm text-gray-600">Tutor: {{ $followup->owner->name ?? 'N/D' }}</p>
                @if($followup->reason)
                    <p class="mt-2 text-gray-700"><span class="font-semibold">Motivo:</span> {{ $followup->reason }}</p>
                @endif
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-mint-700">Adjuntos</p>
                        <h2 class="text-lg font-semibold text-gray-900">Archivos clínicos</h2>
                    </div>
                </div>
                <form action="{{ route('followups.attachments.store', $followup) }}" method="post" class="mt-3 space-y-2" enctype="multipart/form-data">
                    @csrf
                    <input type="text" name="title" class="form-control" placeholder="Título del archivo">
                    <input type="file" name="file" class="form-control">
                    <button type="submit" class="pill-action">Subir adjunto</button>
                </form>
                <div class="divide-y divide-gray-100 mt-4">
                    @forelse($followup->attachments as $attachment)
                        <div class="py-3 flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $attachment->title }}</p>
                                <p class="text-sm text-gray-500">{{ number_format($attachment->size_bytes / 1024, 1) }} KB</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="text-mint-600 font-semibold">Ver</a>
                                <form action="{{ route('followups.attachments.destroy', [$followup, $attachment]) }}" method="post" onsubmit="return confirm('¿Eliminar adjunto?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-danger-500 font-semibold">Eliminar</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No hay archivos adjuntos.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
