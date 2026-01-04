@extends('layouts.app')

@section('content')
<div class="max-w-6xl xl:max-w-7xl mx-auto py-8 space-y-6">
    <div class="rounded-2xl bg-gradient-to-r from-indigo-600 via-sky-500 to-emerald-500 text-white shadow-xl p-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="uppercase tracking-wide text-xs font-semibold opacity-80">Consentimientos informados</p>
            <h1 class="text-3xl font-extrabold mt-1">Generar consentimiento</h1>
            <p class="text-sm text-indigo-100 mt-2">Completa los datos que se insertarán en el documento antes de firmarlo.</p>
        </div>
        @if($patient)
            <div class="bg-white/15 backdrop-blur rounded-xl px-4 py-3 text-sm min-w-[220px]">
                <p class="text-xs uppercase tracking-widest text-indigo-100">Paciente seleccionado</p>
                <p class="text-lg font-semibold">{{ $patient->display_name ?: 'Paciente' }}</p>
                <p class="text-indigo-100">Tutor: {{ $ownerSnapshot['full_name'] ?? $ownerSnapshot['first_name'] ?? 'No asignado' }}</p>
            </div>
        @endif
    </div>

    <form method="POST" action="{{ route('consents.store') }}" class="space-y-6">
        @csrf
        @if($patient)
            <input type="hidden" name="patient_id" value="{{ $patient->id }}">
        @endif

        <div class="bg-white shadow-lg rounded-2xl border border-slate-200/80">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase text-slate-500">Paso 1</p>
                    <h2 class="text-lg font-semibold text-slate-800">Selecciona la plantilla</h2>
                    <p class="text-sm text-slate-500">Solo se muestran las plantillas activas disponibles.</p>
                </div>
                <div class="flex items-center gap-2 text-slate-500 text-sm">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-indigo-50 text-indigo-600 font-semibold">1</span>
                    <span class="hidden sm:block">Configuración inicial</span>
                </div>
            </div>
            <div class="p-6 grid gap-5 lg:grid-cols-[minmax(0,4fr)_minmax(0,1.25fr)] items-start">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-700">Plantilla a usar</label>
                    <select name="template_id" class="mt-1 w-full border-slate-200 rounded-xl px-4 py-3 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach($templates as $template)
                            <option value="{{ $template->id }}" @selected(old('template_id') == $template->id)>{{ $template->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="bg-indigo-50 text-indigo-700 rounded-xl p-4 text-sm">
                    <p class="font-semibold">Tip</p>
                    <p>Las variables de la plantilla se rellenarán con los datos de tutor y paciente que completes más abajo.</p>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="bg-white shadow-lg rounded-2xl border border-slate-200/80">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase text-slate-500">Paso 2</p>
                        <h2 class="text-lg font-semibold text-slate-800">Datos del tutor</h2>
                        <p class="text-sm text-slate-500">Confirma la información de contacto del firmante.</p>
                    </div>
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 font-semibold">2</span>
                </div>
                <div class="p-6 grid gap-4 sm:grid-cols-2">
                    @foreach([
                        'first_name' => 'Nombre',
                        'last_name' => 'Apellido',
                        'document' => 'Documento',
                        'phone' => 'Teléfono',
                        'email' => 'Email',
                        'address' => 'Dirección',
                        'city' => 'Ciudad',
                    ] as $field => $label)
                        <label class="text-sm text-slate-700 space-y-1">
                            <span class="font-medium">{{ $label }}</span>
                            <input
                                type="text"
                                name="owner_snapshot[{{ $field }}]"
                                value="{{ old("owner_snapshot.$field", $ownerSnapshot[$field] ?? '') }}"
                                class="w-full border-slate-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                            />
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="bg-white shadow-lg rounded-2xl border border-slate-200/80">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase text-slate-500">Paso 3</p>
                        <h2 class="text-lg font-semibold text-slate-800">Datos del paciente</h2>
                        <p class="text-sm text-slate-500">Usados para personalizar el cuerpo del consentimiento.</p>
                    </div>
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-sky-50 text-sky-600 font-semibold">3</span>
                </div>
                <div class="p-6 grid gap-4 sm:grid-cols-2">
                    @foreach([
                        'name' => 'Nombre',
                        'species' => 'Especie',
                        'breed' => 'Raza',
                        'sex' => 'Sexo',
                        'age' => 'Edad',
                        'weight' => 'Peso',
                        'color' => 'Color',
                        'microchip' => 'Microchip',
                    ] as $field => $label)
                        <label class="text-sm text-slate-700 space-y-1">
                            <span class="font-medium">{{ $label }}</span>
                            <input
                                type="text"
                                name="pet_snapshot[{{ $field }}]"
                                value="{{ old("pet_snapshot.$field", $petSnapshot[$field] ?? '') }}"
                                class="w-full border-slate-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                            />
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ url()->previous() }}" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:text-slate-800 hover:border-slate-300 transition">Cancelar</a>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-emerald-500 text-white font-semibold shadow-md hover:shadow-lg transition">Generar consentimiento</button>
        </div>
    </form>
</div>
@endsection
