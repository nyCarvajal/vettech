@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4">
    <div class="bg-gradient-to-r from-purple-50 to-white border border-purple-100 shadow-sm rounded-2xl p-6 space-y-6">
        <div class="flex items-center gap-3">
            <div class="p-3 rounded-xl bg-purple-100 text-purple-600">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                    <path d="M4.5 3.75A2.25 2.25 0 0 0 2.25 6v4.184c.176-.077.359-.139.547-.183L9 8.684V6a2.25 2.25 0 0 0-2.25-2.25h-2.25Z"/>
                    <path d="M21.75 8.25V6a2.25 2.25 0 0 0-2.25-2.25H12A2.25 2.25 0 0 0 9.75 6v2.684l6.204 1.517a2.998 2.998 0 0 1 1.968 1.96l3.829.922v-4.833Z"/>
                    <path d="M2.25 18.75v-6.814a1.5 1.5 0 0 1 1.848-1.456l8.652 2.164a.75.75 0 0 1 .57.728v5.128H5.25a3 3 0 0 1-3-3ZM14.25 18.75V13.64a.75.75 0 0 1 .93-.728l5.652 1.413A1.5 1.5 0 0 1 22.5 15.776v1.974a3 3 0 0 1-3 3h-5.25Z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Admitir paciente</h1>
                <p class="text-sm text-purple-700">Registra la estancia hospitalaria con los datos básicos en un solo lugar.</p>
            </div>
        </div>

        <form method="post" action="{{ route('hospital.stays.store') }}" class="space-y-6">
            @csrf

            @if ($patient ?? false)
                <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                <div class="bg-white border border-purple-100 rounded-xl p-4 shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="p-2 rounded-full bg-purple-100 text-purple-500">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                <path d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.75 20.1a8.25 8.25 0 0 1 16.5 0 .9.9 0 0 1-.9.9h-14.7a.9.9 0 0 1-.9-.9Z" />
                            </svg>
                        </div>
                        <div class="flex-1 space-y-1">
                            <p class="text-xs font-semibold text-purple-600 uppercase tracking-wide">Paciente</p>
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-lg font-semibold text-gray-900">{{ $patient->display_name }}</h2>
                                <span class="text-xs text-purple-700 bg-purple-50 px-3 py-1 rounded-full">ID #{{ $patient->id }}</span>
                            </div>
                            <div class="text-sm text-gray-700 flex flex-wrap gap-3">
                                <span>{{ $patient->species?->name ?? 'Especie no registrada' }}</span>
                                <span class="text-gray-400">•</span>
                                <span>{{ optional($patient->owner)->name ?? 'Sin tutor' }}</span>
                                @if (optional($patient->owner)->phone)
                                    <span class="text-gray-400">•</span>
                                    <span>Tel: {{ optional($patient->owner)->phone }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="space-y-2">
                    <label class="text-sm font-medium text-gray-700">Paciente</label>
                    <input name="patient_id" value="{{ old('patient_id', request('patient_id')) }}" placeholder="ID del paciente" class="border border-purple-200 focus:ring-2 focus:ring-purple-400 focus:border-purple-400 rounded-lg p-3 w-full" required>
                    <p class="text-xs text-gray-500">Ingresa el identificador del paciente si no vienes desde su ficha.</p>
                </div>
            @endif

            <div class="grid md:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-gray-700">Fecha de admisión</label>
                    <input type="datetime-local" name="admitted_at" value="{{ old('admitted_at', now()->format('Y-m-d\\TH:i')) }}" class="border border-purple-200 focus:ring-2 focus:ring-purple-400 focus:border-purple-400 rounded-lg p-3 w-full" required>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium text-gray-700">Jaula / alojamiento</label>
                    <select name="cage_id" class="border border-purple-200 focus:ring-2 focus:ring-purple-400 focus:border-purple-400 rounded-lg p-3 w-full" {{ $cages->isEmpty() ? 'disabled' : '' }}>
                        @if($cages->isEmpty())
                            <option value="">No hay jaulas activas disponibles</option>
                        @else
                            <option value="">Selecciona una jaula</option>
                            @foreach($cages as $cage)
                                <option value="{{ $cage->id }}">{{ $cage->name }} ({{ $cage->location }})</option>
                            @endforeach
                        @endif
                    </select>
                    <p class="text-xs text-purple-700">La jaula es el cubículo o habitación donde se ubicará el paciente durante su estancia.</p>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-gray-700">Severidad</label>
                    <select name="severity" class="border border-purple-200 focus:ring-2 focus:ring-purple-400 focus:border-purple-400 rounded-lg p-3 w-full">
                        <option value="stable">Estable</option>
                        <option value="observation">Observación</option>
                        <option value="critical">Crítico</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium text-gray-700">Diagnóstico</label>
                    <textarea name="diagnosis" class="border border-purple-200 focus:ring-2 focus:ring-purple-400 focus:border-purple-400 rounded-lg p-3 w-full" rows="2" placeholder="Diagnóstico inicial"></textarea>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-gray-700">Plan</label>
                    <textarea name="plan" class="border border-purple-200 focus:ring-2 focus:ring-purple-400 focus:border-purple-400 rounded-lg p-3 w-full" rows="3" placeholder="Tratamientos o cuidados sugeridos"></textarea>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium text-gray-700">Dieta</label>
                    <textarea name="diet" class="border border-purple-200 focus:ring-2 focus:ring-purple-400 focus:border-purple-400 rounded-lg p-3 w-full" rows="3" placeholder="Alimentación recomendada"></textarea>
                </div>
            </div>

            <div class="flex justify-end">
                <button class="px-5 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg shadow-sm transition-colors inline-flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                        <path d="M12 2.25a9.75 9.75 0 1 0 9.75 9.75A9.76 9.76 0 0 0 12 2.25Zm.75 5.25a.75.75 0 1 0-1.5 0v3.75H7.5a.75.75 0 0 0 0 1.5h3.75v3.75a.75.75 0 0 0 1.5 0v-3.75H16.5a.75.75 0 0 0 0-1.5h-3.75Z" />
                    </svg>
                    Guardar admisión
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
