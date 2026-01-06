@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ status: '{{ $followup->improved_status ?? 'unknown' }}' }">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <p class="text-sm text-gray-500">Control posterior</p>
            <h1 class="text-2xl font-bold text-gray-900">{{ $mode === 'create' ? 'Nuevo control' : 'Editar control ' . $followup->code }}</h1>
        </div>
        <a href="{{ route('followups.index') }}" class="pill-action">Volver</a>
    </div>

    <form method="post" action="{{ $mode === 'create' ? route('followups.store') : route('followups.update', $followup) }}" class="space-y-6">
        @csrf
        @if($mode === 'edit')
            @method('PUT')
        @endif

        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-mint-700">Datos básicos</p>
                        <h2 class="text-lg font-semibold text-gray-900">Paciente y consulta</h2>
                    </div>
                </div>

                <div class="grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="form-label">Paciente</label>
                        <select name="patient_id" class="form-control">
                            <option value="">Seleccione</option>
                            @foreach($patients as $p)
                                <option value="{{ $p->id }}" @selected(old('patient_id', $followup->patient_id ?? $patient?->id) == $p->id)>{{ $p->display_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Consulta relacionada</label>
                        <input type="number" name="consultation_id" class="form-control" value="{{ old('consultation_id', $followup->consultation_id ?? $consultation?->id) }}" placeholder="#">
                        <p class="text-xs text-gray-500 mt-1">Si proviene de una consulta previa, referencia su ID.</p>
                    </div>
                    <div>
                        <label class="form-label">Fecha y hora</label>
                        <input type="datetime-local" name="followup_at" value="{{ old('followup_at', optional($followup->followup_at)->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i')) }}" class="form-control" required>
                    </div>
                    <div>
                        <label class="form-label">Responsable</label>
                        <input type="text" name="performed_by" class="form-control" value="{{ old('performed_by', $followup->performed_by) }}" placeholder="Nombre del profesional">
                        <input type="text" name="performed_by_license" class="form-control mt-2" value="{{ old('performed_by_license', $followup->performed_by_license) }}" placeholder="Licencia (opcional)">
                    </div>
                    <div class="md:col-span-2">
                        <label class="form-label">Motivo u objetivo</label>
                        <input type="text" name="reason" class="form-control" value="{{ old('reason', $followup->reason) }}" placeholder="Ej. Control posquirúrgico">
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm space-y-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-mint-700">Evolución</p>
                    <h2 class="text-lg font-semibold text-gray-900">¿Mejoró?</h2>
                </div>

                <div class="grid gap-3">
                    <div class="flex gap-3 flex-wrap">
                        @foreach(['yes' => 'Sí', 'partial' => 'Parcial', 'no' => 'No', 'unknown' => 'No sabe'] as $key => $label)
                            <label class="inline-flex items-center gap-2 rounded-full border border-gray-200 px-3 py-2 cursor-pointer">
                                <input type="radio" name="improved_status" value="{{ $key }}" x-model="status" @checked(old('improved_status', $followup->improved_status ?? 'unknown') === $key)>
                                <span class="font-semibold text-gray-800">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    <div x-show="status !== 'unknown'" class="space-y-2">
                        <label class="form-label">Nivel (0-10)</label>
                        <input type="number" name="improved_score" min="0" max="10" value="{{ old('improved_score', $followup->improved_score) }}" class="form-control">
                    </div>
                    <div>
                        <label class="form-label">Observaciones</label>
                        <textarea name="observations" rows="4" class="form-control" placeholder="Notas clínicas">{{ old('observations', $followup->observations) }}</textarea>
                    </div>
                    <div>
                        <label class="form-label">Plan / recomendaciones</label>
                        <textarea name="plan" rows="3" class="form-control" placeholder="Cambios de tratamiento, cuidados en casa">{{ old('plan', $followup->plan) }}</textarea>
                    </div>
                    <div>
                        <label class="form-label">Próximo control</label>
                        <input type="datetime-local" name="next_followup_at" value="{{ old('next_followup_at', optional($followup->next_followup_at)->format('Y-m-d\TH:i')) }}" class="form-control">
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-mint-700">Signos vitales</p>
                    <h2 class="text-lg font-semibold text-gray-900">Monitoreo</h2>
                </div>
            </div>
            <div class="grid gap-3 md:grid-cols-4">
                @php $vitals = old('vitals', $followup->vitals?->toArray() ?? []); @endphp
                <div>
                    <label class="form-label">Temperatura (°C)</label>
                    <input type="number" step="0.1" name="vitals[temperature_c]" value="{{ $vitals['temperature_c'] ?? '' }}" class="form-control" min="30" max="45">
                </div>
                <div>
                    <label class="form-label">FC (lpm)</label>
                    <input type="number" name="vitals[heart_rate_bpm]" value="{{ $vitals['heart_rate_bpm'] ?? '' }}" class="form-control" min="0" max="400">
                </div>
                <div>
                    <label class="form-label">FR (rpm)</label>
                    <input type="number" name="vitals[respiratory_rate_rpm]" value="{{ $vitals['respiratory_rate_rpm'] ?? '' }}" class="form-control" min="0" max="200">
                </div>
                <div>
                    <label class="form-label">Peso (kg)</label>
                    <input type="number" step="0.01" name="vitals[weight_kg]" value="{{ $vitals['weight_kg'] ?? '' }}" class="form-control" min="0" max="200">
                </div>
                <div>
                    <label class="form-label">Hidratación</label>
                    <select name="vitals[hydration]" class="form-control">
                        @foreach(['unknown' => 'No evaluado', 'normal' => 'Normal', 'mild_dehydration' => 'Leve', 'moderate' => 'Moderada', 'severe' => 'Severa'] as $key => $label)
                            <option value="{{ $key }}" @selected(($vitals['hydration'] ?? 'unknown') === $key)> {{ $label }} </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Mucosas</label>
                    <select name="vitals[mucous_membranes]" class="form-control">
                        @foreach(['unknown' => 'No evaluado', 'pink' => 'Rosas', 'pale' => 'Pálidas', 'icteric' => 'Ictéricas', 'cyanotic' => 'Cianóticas', 'hyperemic' => 'Hiperémicas'] as $key => $label)
                            <option value="{{ $key }}" @selected(($vitals['mucous_membranes'] ?? 'unknown') === $key)> {{ $label }} </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">TRC (seg)</label>
                    <input type="number" step="0.1" name="vitals[capillary_refill_time_sec]" value="{{ $vitals['capillary_refill_time_sec'] ?? '' }}" class="form-control" min="0" max="10">
                </div>
                <div>
                    <label class="form-label">Dolor (0-10)</label>
                    <input type="number" name="vitals[pain_score_0_10]" value="{{ $vitals['pain_score_0_10'] ?? '' }}" class="form-control" min="0" max="10">
                </div>
                <div>
                    <label class="form-label">PA Sistólica</label>
                    <input type="number" name="vitals[blood_pressure_sys]" value="{{ $vitals['blood_pressure_sys'] ?? '' }}" class="form-control" min="0">
                </div>
                <div>
                    <label class="form-label">PA Diastólica</label>
                    <input type="number" name="vitals[blood_pressure_dia]" value="{{ $vitals['blood_pressure_dia'] ?? '' }}" class="form-control" min="0">
                </div>
                <div>
                    <label class="form-label">MAP</label>
                    <input type="number" name="vitals[blood_pressure_map]" value="{{ $vitals['blood_pressure_map'] ?? '' }}" class="form-control" min="0">
                </div>
                <div>
                    <label class="form-label">SpO₂ (%)</label>
                    <input type="number" name="vitals[o2_saturation_percent]" value="{{ $vitals['o2_saturation_percent'] ?? '' }}" class="form-control" min="0" max="100">
                </div>
                <div class="md:col-span-2">
                    <label class="form-label">Notas de signos vitales</label>
                    <textarea name="vitals[notes]" rows="3" class="form-control">{{ $vitals['notes'] ?? '' }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="pill-action">{{ $mode === 'create' ? 'Guardar control' : 'Actualizar' }}</button>
        </div>
    </form>
</div>
@endsection
