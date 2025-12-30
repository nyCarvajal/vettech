@extends('layouts.app')

@section('content')
@php
    $patientName = trim(($historia->paciente->nombres ?? '') . ' ' . ($historia->paciente->apellidos ?? '')) ?: 'Sin paciente';
    $tutor = $historia->paciente?->owner;
@endphp
<div class="space-y-6">
    <div class="rounded-3xl bg-gradient-to-r from-mint-500 via-mint-600 to-emerald-500 p-6 shadow-lg text-white">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="space-y-1">
                <p class="text-xs uppercase tracking-[0.2em] text-white/80">Historia clínica</p>
                <h1 class="text-3xl font-bold">#{{ $historia->id }} • {{ $patientName }}</h1>
                <p class="text-white/90">Tutor: {{ $tutor->name ?? 'Sin tutor' }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2 text-sm">
                <a
                    href="{{ route('historias-clinicas.edit', $historia) }}"
                    class="inline-flex items-center gap-2 rounded-full bg-emerald-600 px-4 py-2 font-semibold text-white shadow-sm transition hover:bg-emerald-500"
                    style="background-color: #059669"
                >Editar</a>
                <a
                    href="{{ route('historias-clinicas.recetarios.create', $historia) }}"
                    class="inline-flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2 font-semibold text-white shadow-sm transition hover:bg-blue-500"
                    style="background-color: #2563eb"
                >Agregar recetario</a>
                <a
                    href="{{ route('historias-clinicas.remisiones.create', $historia) }}"
                    class="inline-flex items-center gap-2 rounded-full bg-amber-500 px-4 py-2 font-semibold text-white shadow-sm transition hover:bg-amber-400"
                    style="background-color: #f59e0b"
                >Nueva remisión</a>
                <a
                    href="{{ route('historias-clinicas.pdf', $historia) }}"
                    class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 font-semibold text-emerald-700 shadow-sm transition hover:bg-emerald-50"
                    style="background-color: #ffffff; color: #047857"
                >Imprimir PDF</a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-green-800 shadow-sm">{{ session('success') }}</div>
    @endif

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-mint-700">Paciente</p>
            <h2 class="text-lg font-semibold text-gray-900">{{ $patientName }}</h2>
            <p class="text-sm text-gray-600">{{ $historia->paciente->especie ?? 'Especie no definida' }}</p>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-mint-700">Tutor</p>
            <h2 class="text-lg font-semibold text-gray-900">{{ $tutor->name ?? 'Sin tutor' }}</h2>
            <p class="text-sm text-gray-600">Tel: {{ $tutor->phone ?: 'N/D' }} · WhatsApp: {{ $tutor->whatsapp ?: 'N/D' }}</p>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-mint-700">Actividad</p>
            <div class="mt-2 grid w-full grid-cols-1 gap-2 text-sm text-gray-700 sm:grid-cols-3">
                <span class="rounded-full bg-mint-50 px-3 py-2 text-center font-semibold text-mint-700">{{ $historia->paraclinicos->count() }} paraclínicos</span>
                <span class="rounded-full bg-amber-50 px-3 py-2 text-center font-semibold text-amber-700">{{ $historia->diagnosticos->count() }} diagnósticos</span>
                <span class="rounded-full bg-blue-50 px-3 py-2 text-center font-semibold text-blue-700">{{ $prescriptions->count() }} recetarios</span>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-3">
            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between pb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Motivo y antecedentes</h3>
                    <span class="w-full rounded-full bg-mint-50 px-3 py-1 text-center text-xs font-semibold uppercase tracking-wide text-mint-700 md:w-auto">Consulta</span>
                </div>
                <div class="grid gap-4 text-sm text-gray-700 md:grid-cols-2">
                    <p class="text-base"><strong class="text-gray-900">Motivo de consulta:</strong> {{ $historia->motivo_consulta ?: 'Sin registrar' }}</p>
                    <p class="text-base md:col-span-2"><strong class="text-gray-900">Antecedentes / Enfermedad actual:</strong> {{ $historia->enfermedad_actual ?: 'Sin registrar' }}</p>
                    <p class="text-base"><strong class="text-gray-900">Antecedentes farmacológicos:</strong> {{ $historia->antecedentes_farmacologicos ?: 'N/D' }}</p>
                    <p class="text-base"><strong class="text-gray-900">Antecedentes patológicos:</strong> {{ $historia->antecedentes_patologicos ?: 'N/D' }}</p>
                </div>
            </div>
        </div>

        <div class="space-y-4 lg:col-span-2">
            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between pb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Paraclínicos solicitados</h3>
                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-blue-700">Exámenes</span>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($historia->paraclinicos as $paraclinico)
                        <div class="flex items-start justify-between gap-3 py-3">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $paraclinico->nombre }}</p>
                                <p class="text-sm text-gray-600">{{ $paraclinico->resultado ?: 'Pendiente' }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="py-2 text-sm text-gray-500">No hay paraclínicos agregados.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between pb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Diagnósticos</h3>
                    <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-amber-700">Clínica</span>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($historia->diagnosticos as $diagnostico)
                        <div class="py-3">
                            <p class="font-semibold text-gray-900">{{ $diagnostico->descripcion }}</p>
                            <p class="text-sm text-gray-600">{{ $diagnostico->codigo ?: 'Sin código' }} · {{ $diagnostico->confirmado ? 'Confirmado' : 'Presuntivo' }}</p>
                        </div>
                    @empty
                        <p class="py-2 text-sm text-gray-500">No hay diagnósticos registrados.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between pb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Plan y análisis</h3>
                    <span class="rounded-full bg-purple-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-purple-700">Seguimiento</span>
                </div>
                <div class="space-y-4 text-sm text-gray-700">
                    <div>
                        <p class="font-semibold text-gray-900">Análisis</p>
                        <p class="mt-1 text-gray-600">{{ $historia->analisis ?: 'Sin registrar' }}</p>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Plan procedimientos</p>
                        <p class="mt-1 text-gray-600">{{ $historia->plan_procedimientos ?: 'Sin registrar' }}</p>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Plan medicamentos</p>
                        <p class="mt-1 text-gray-600">{{ $historia->plan_medicamentos ?: 'Sin registrar' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between pb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Tutor</h3>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-700">Contacto</span>
                </div>
                <div class="space-y-2 text-sm text-gray-700">
                    <p class="font-semibold text-gray-900">{{ $tutor->name ?? 'Sin tutor' }}</p>
                    <p class="text-gray-600">Tel: {{ $tutor->phone ?: 'N/D' }}</p>
                    <p class="text-gray-600">WhatsApp: {{ $tutor->whatsapp ?: 'N/D' }}</p>
                    <p class="text-gray-600">Correo: {{ $tutor->email ?: 'N/D' }}</p>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between pb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Recetarios</h3>
                    <span class="rounded-full bg-mint-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-mint-700">Tratamientos</span>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($prescriptions as $prescription)
                        <div class="py-3 space-y-2">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-gray-900">Recetario #{{ $prescription->id }}</p>
                                    <p class="text-sm text-gray-600">{{ optional($prescription->professional)->name }}</p>
                                </div>
                                <div class="flex items-center gap-2 text-xs font-semibold">
                                    <a class="rounded-full border border-blue-200 px-3 py-1 text-blue-700 hover:bg-blue-50 transition" href="{{ route('historias-clinicas.recetarios.print', $prescription) }}">PDF</a>
                                    <form method="post" action="{{ route('historias-clinicas.recetarios.facturar', $prescription) }}">
                                        @csrf
                                        <button class="rounded-full border border-emerald-200 px-3 py-1 text-emerald-700 hover:bg-emerald-50 transition" type="submit">Facturar</button>
                                    </form>
                                </div>
                            </div>
                            <ul class="space-y-1 text-sm text-gray-600">
                                @foreach($prescription->items as $item)
                                    <li class="flex items-start justify-between gap-2">
                                        <span>{{ $item->is_manual ? $item->manual_name : optional($item->product)->name }} ({{ $item->qty_requested }})</span>
                                        @if($item->is_manual)
                                            <span class="rounded-full bg-gray-100 px-3 py-0.5 text-xs font-semibold text-gray-700">No facturable</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @empty
                        <p class="py-2 text-sm text-gray-500">Sin recetarios aún.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between pb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Remisiones de exámenes</h3>
                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-indigo-700">Exámenes</span>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($referrals as $referral)
                        <div class="flex items-center justify-between py-3">
                            <div>
                                <p class="font-semibold text-gray-900">Remisión #{{ $referral->id }}</p>
                                <p class="text-sm text-gray-600">{{ $referral->created_at?->format('d/m/Y') }}</p>
                            </div>
                            <a class="rounded-full border border-blue-200 px-3 py-1 text-blue-700 hover:bg-blue-50 transition" href="{{ route('historias-clinicas.remisiones.print', $referral) }}">PDF</a>
                        </div>
                    @empty
                        <p class="py-2 text-sm text-gray-500">Sin remisiones registradas.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
