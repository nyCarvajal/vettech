@php
    $prefill = $prefill ?? [];
    $lockOwnerPet = isset($prefill['pet_id']);
    $initialType = old('type', $certificate->type ?? 'national_co');
    $initialLanguage = old('language', $certificate->language ?? 'es');
    $initialVetName = old('vet_name', $certificate->vet_name ?? ($defaultClinic['vet_name'] ?? ''));
    $initialVetLicense = old('vet_license', $certificate->vet_license ?? ($defaultClinic['vet_license'] ?? ''));
    $initialOriginDepartment = old('origin_department_id', $certificate->origin_department_id ?? '');
    $initialDestinationDepartment = old('destination_department_id', $certificate->destination_department_id ?? '');
    $initialOriginMunicipality = old('origin_municipality_id', $certificate->origin_municipality_id ?? '');
    $initialDestinationMunicipality = old('destination_municipality_id', $certificate->destination_municipality_id ?? '');
@endphp

@csrf
<div
    class="space-y-6"
    x-data="travelCertificateForm({
        type: '{{ $initialType }}',
        language: '{{ $initialLanguage }}',
        lockOwner: {{ $lockOwnerPet ? 'true' : 'false' }},
        originDepartment: '{{ $initialOriginDepartment }}',
        destinationDepartment: '{{ $initialDestinationDepartment }}',
        originMunicipality: '{{ $initialOriginMunicipality }}',
        destinationMunicipality: '{{ $initialDestinationMunicipality }}',
        vetName: @js($initialVetName),
        vetLicense: @js($initialVetLicense),
        vets: @js($vets->map(fn ($vet) => [
            'id' => $vet->id,
            'name' => trim(($vet->nombre ?? '') . ' ' . ($vet->apellidos ?? '')),
            'license' => $vet->firma_medica_texto ?? $vet->numero_identificacion ?? null,
        ])),
        defaultVetId: {{ optional($user)->id ?? 'null' }},
    })"
>
    <div class="bg-gradient-to-r from-indigo-50 via-white to-teal-50 p-6 rounded-2xl shadow-sm border border-indigo-100">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-xs uppercase tracking-wide text-indigo-500 font-semibold">Certificado de viaje</p>
                <h2 class="text-2xl font-bold text-slate-900">Configura el certificado</h2>
            </div>
            <div class="flex items-center space-x-3">
                <div class="px-3 py-1 rounded-full bg-white text-indigo-600 text-sm font-semibold border border-indigo-100">Paso a paso</div>
                <div class="px-3 py-1 rounded-full bg-white text-teal-600 text-sm font-semibold border border-teal-100">Seguro</div>
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl p-4 border border-indigo-100 shadow-sm">
                <label class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Tipo</label>
                <select name="type" x-model="state.type" class="mt-2 w-full rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-200">
                    <option value="national_co">Nacional (CO)</option>
                    <option value="international">Internacional</option>
                </select>
            </div>
            <div class="bg-white rounded-xl p-4 border border-indigo-100 shadow-sm">
                <label class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Idioma</label>
                <select name="language" x-model="state.language" class="mt-2 w-full rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-200">
                    <option value="es">ES</option>
                    <option value="en">EN</option>
                    <option value="es_en">ES + EN</option>
                </select>
            </div>
            <div class="bg-white rounded-xl p-4 border border-indigo-100 shadow-sm">
                <label class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Salida</label>
                <div class="flex space-x-2 mt-2">
                    <input type="date" name="travel_departure_date" value="{{ old('travel_departure_date', optional($certificate->travel_departure_date ?? null)->format('Y-m-d')) }}" class="flex-1 rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-200">
                    <input type="time" name="travel_departure_time" value="{{ old('travel_departure_time', optional($certificate->travel_departure_time ?? null)->format('H:i')) }}" class="w-28 rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-200">
                </div>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-indigo-500 font-semibold">Clínica y MV</p>
                        <h3 class="text-lg font-bold text-slate-900">Datos de la clínica</h3>
                    </div>
                    <div class="text-xs text-slate-500">Usando datos de la sesión</div>
                </div>
                <div class="grid md:grid-cols-2 gap-4">
                    <input type="text" name="clinic_name" placeholder="Nombre de la clínica" value="{{ old('clinic_name', $certificate->clinic_name ?? $defaultClinic['name'] ?? '') }}" class="rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-200">
                    <input type="text" name="clinic_nit" placeholder="NIT" value="{{ old('clinic_nit', $certificate->clinic_nit ?? $defaultClinic['nit'] ?? '') }}" class="rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-200">
                    <input type="text" name="clinic_address" placeholder="Dirección" value="{{ old('clinic_address', $certificate->clinic_address ?? $defaultClinic['address'] ?? '') }}" class="rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-200">
                    <input type="text" name="clinic_phone" placeholder="Teléfono" value="{{ old('clinic_phone', $certificate->clinic_phone ?? $defaultClinic['phone'] ?? '') }}" class="rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-200">
                    <input type="text" name="clinic_city" placeholder="Ciudad" value="{{ old('clinic_city', $certificate->clinic_city ?? $defaultClinic['city'] ?? '') }}" class="rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-200">
                </div>

                <div class="mt-6 grid md:grid-cols-3 gap-4 items-end">
                    <div class="md:col-span-1">
                        <label class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Médico veterinario</label>
                        <select x-model.number="state.selectedVet" @change="applyVet()" class="mt-2 w-full rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-200">
                            <template x-for="vet in state.vets" :key="vet.id">
                                <option :value="vet.id" x-text="vet.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Nombre</label>
                        <input type="text" name="vet_name" x-model="state.vetName" readonly class="mt-2 rounded-lg border-slate-200 bg-slate-50 focus:border-indigo-500 focus:ring-indigo-200">
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Licencia</label>
                        <input type="text" name="vet_license" x-model="state.vetLicense" readonly class="mt-2 rounded-lg border-slate-200 bg-slate-50 focus:border-indigo-500 focus:ring-indigo-200">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-teal-500 font-semibold">Tutor</p>
                        <h3 class="text-lg font-bold text-slate-900">Datos del tutor</h3>
                    </div>
                    @if($lockOwnerPet)
                        <span class="text-xs text-slate-500">Usando datos del paciente (solo lectura)</span>
                    @endif
                </div>
                <div class="grid md:grid-cols-2 gap-4">
                    <input type="text" name="owner_name" placeholder="Nombre" value="{{ old('owner_name', $certificate->owner_name ?? $prefill['owner_name'] ?? '') }}" @if($lockOwnerPet) readonly @endif class="rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-200 @if($lockOwnerPet) bg-slate-50 text-slate-500 @endif">
                    <input type="text" name="owner_document_number" placeholder="Documento" value="{{ old('owner_document_number', $certificate->owner_document_number ?? $prefill['owner_document_number'] ?? '') }}" @if($lockOwnerPet) readonly @endif class="rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-200 @if($lockOwnerPet) bg-slate-50 text-slate-500 @endif">
                    <input type="text" name="owner_phone" placeholder="Teléfono" value="{{ old('owner_phone', $certificate->owner_phone ?? $prefill['owner_phone'] ?? '') }}" @if($lockOwnerPet) readonly @endif class="rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-200 @if($lockOwnerPet) bg-slate-50 text-slate-500 @endif">
                    <input type="email" name="owner_email" placeholder="Email" value="{{ old('owner_email', $certificate->owner_email ?? $prefill['owner_email'] ?? '') }}" @if($lockOwnerPet) readonly @endif class="rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-200 @if($lockOwnerPet) bg-slate-50 text-slate-500 @endif">
                    <input type="text" name="owner_address" placeholder="Dirección" value="{{ old('owner_address', $certificate->owner_address ?? $prefill['owner_address'] ?? '') }}" @if($lockOwnerPet) readonly @endif class="rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-200 @if($lockOwnerPet) bg-slate-50 text-slate-500 @endif">
                    <input type="text" name="owner_city" placeholder="Ciudad" value="{{ old('owner_city', $certificate->owner_city ?? $prefill['owner_city'] ?? '') }}" @if($lockOwnerPet) readonly @endif class="rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-200 @if($lockOwnerPet) bg-slate-50 text-slate-500 @endif">
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-amber-500 font-semibold">Mascota</p>
                        <h3 class="text-lg font-bold text-slate-900">Datos de la mascota</h3>
                    </div>
                    @if($lockOwnerPet)
                        <span class="text-xs text-slate-500">Bloqueado desde el paciente</span>
                    @endif
                </div>
                <input type="hidden" name="pet_id" value="{{ old('pet_id', $certificate->pet_id ?? $prefill['pet_id'] ?? '') }}">
                <div class="grid md:grid-cols-3 gap-4">
                    <input type="text" name="pet_name" placeholder="Nombre" value="{{ old('pet_name', $certificate->pet_name ?? $prefill['pet_name'] ?? '') }}" @if($lockOwnerPet) readonly @endif class="rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-200 @if($lockOwnerPet) bg-slate-50 text-slate-500 @endif">
                    <input type="text" name="pet_species" placeholder="Especie" value="{{ old('pet_species', $certificate->pet_species ?? $prefill['pet_species'] ?? '') }}" @if($lockOwnerPet) readonly @endif class="rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-200 @if($lockOwnerPet) bg-slate-50 text-slate-500 @endif">
                    <input type="text" name="pet_breed" placeholder="Raza" value="{{ old('pet_breed', $certificate->pet_breed ?? $prefill['pet_breed'] ?? '') }}" @if($lockOwnerPet) readonly @endif class="rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-200 @if($lockOwnerPet) bg-slate-50 text-slate-500 @endif">
                    <input type="text" name="pet_sex" placeholder="Sexo" value="{{ old('pet_sex', $certificate->pet_sex ?? $prefill['pet_sex'] ?? '') }}" @if($lockOwnerPet) readonly @endif class="rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-200 @if($lockOwnerPet) bg-slate-50 text-slate-500 @endif">
                    <input type="number" name="pet_age_months" placeholder="Edad (meses)" value="{{ old('pet_age_months', $certificate->pet_age_months ?? $prefill['pet_age_months'] ?? '') }}" @if($lockOwnerPet) readonly @endif class="rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-200 @if($lockOwnerPet) bg-slate-50 text-slate-500 @endif">
                    <input type="number" step="0.01" name="pet_weight_kg" placeholder="Peso (kg)" value="{{ old('pet_weight_kg', $certificate->pet_weight_kg ?? $prefill['pet_weight_kg'] ?? '') }}" @if($lockOwnerPet) readonly @endif class="rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-200 @if($lockOwnerPet) bg-slate-50 text-slate-500 @endif">
                    <input type="text" name="pet_color" placeholder="Color" value="{{ old('pet_color', $certificate->pet_color ?? $prefill['pet_color'] ?? '') }}" @if($lockOwnerPet) readonly @endif class="rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-200 @if($lockOwnerPet) bg-slate-50 text-slate-500 @endif">
                    <input type="text" name="pet_microchip" placeholder="Microchip" value="{{ old('pet_microchip', $certificate->pet_microchip ?? $prefill['pet_microchip'] ?? '') }}" @if($lockOwnerPet) readonly @endif class="rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-200 @if($lockOwnerPet) bg-slate-50 text-slate-500 @endif">
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6" x-cloak>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-indigo-500 font-semibold">Viaje</p>
                        <h3 class="text-lg font-bold text-slate-900">Origen y destino</h3>
                    </div>
                    <span class="text-xs text-slate-500" x-text="state.type === 'national_co' ? 'Ámbito nacional' : 'Ámbito internacional'"></span>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <h4 class="text-sm font-semibold text-indigo-700">Origen</h4>
                        <template x-if="state.type === 'national_co'">
                            <div class="space-y-2">
                                <label class="text-xs text-slate-500">Departamento</label>
                                <select name="origin_department_id" x-model="state.originDepartment" @change="loadMunicipalities('origin')" class="w-full rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-200">
                                    <option value="">Seleccione</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                                <label class="text-xs text-slate-500">Ciudad / Municipio</label>
                                <select name="origin_municipality_id" x-model="state.originMunicipality" class="w-full rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-200">
                                    <option value="">Seleccione</option>
                                    <template x-for="city in state.originMunicipalities" :key="city.id">
                                        <option :value="city.id" x-text="city.nombre || city.name"></option>
                                    </template>
                                </select>
                            </div>
                        </template>
                        <template x-if="state.type === 'international'">
                            <div class="space-y-2">
                                <label class="text-xs text-slate-500">País</label>
                                <select name="origin_country_code" class="w-full rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-200">
                                    <option value="">Seleccione</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->code2 }}" @selected(old('origin_country_code', $certificate->origin_country_code ?? '')==$country->code2)>{{ $country->name_es }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </template>
                    </div>
                    <div class="space-y-3">
                        <h4 class="text-sm font-semibold text-indigo-700">Destino</h4>
                        <template x-if="state.type === 'national_co'">
                            <div class="space-y-2">
                                <label class="text-xs text-slate-500">Departamento</label>
                                <select name="destination_department_id" x-model="state.destinationDepartment" @change="loadMunicipalities('destination')" class="w-full rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-200">
                                    <option value="">Seleccione</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                                <label class="text-xs text-slate-500">Ciudad / Municipio</label>
                                <select name="destination_municipality_id" x-model="state.destinationMunicipality" class="w-full rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-200">
                                    <option value="">Seleccione</option>
                                    <template x-for="city in state.destinationMunicipalities" :key="city.id">
                                        <option :value="city.id" x-text="city.nombre || city.name"></option>
                                    </template>
                                </select>
                            </div>
                        </template>
                        <template x-if="state.type === 'international'">
                            <div class="space-y-2">
                                <label class="text-xs text-slate-500">País</label>
                                <select name="destination_country_code" class="w-full rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-200">
                                    <option value="">Seleccione</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->code2 }}" @selected(old('destination_country_code', $certificate->destination_country_code ?? '')==$country->code2)>{{ $country->name_es }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-4 mt-6">
                    <select name="transport_type" class="rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-200">
                        <option value="">Medio</option>
                        <option value="air" @selected(old('transport_type', $certificate->transport_type ?? '')==='air')>Aéreo</option>
                        <option value="land" @selected(old('transport_type', $certificate->transport_type ?? '')==='land')>Terrestre</option>
                        <option value="other" @selected(old('transport_type', $certificate->transport_type ?? '')==='other')>Otro</option>
                    </select>
                    <input type="text" name="transport_company" placeholder="Empresa" value="{{ old('transport_company', $certificate->transport_company ?? '') }}" class="rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-200">
                    <input type="text" name="flight_number" placeholder="Vuelo" value="{{ old('flight_number', $certificate->flight_number ?? '') }}" class="rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-200">
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-rose-500 font-semibold">Evaluación</p>
                        <h3 class="text-lg font-bold text-slate-900">Examen clínico</h3>
                    </div>
                    <label class="inline-flex items-center space-x-2 text-sm text-slate-600">
                        <input type="checkbox" name="fit_for_travel" value="1" @checked(old('fit_for_travel', $certificate->fit_for_travel ?? true)) class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span>Apto para viajar</span>
                    </label>
                </div>
                <div class="grid md:grid-cols-2 gap-4">
                    <input type="datetime-local" name="clinical_exam_at" value="{{ old('clinical_exam_at', optional($certificate->clinical_exam_at ?? now())->format('Y-m-d\\TH:i')) }}" class="rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-200">
                    <textarea name="clinical_notes" class="rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-200" placeholder="Notas" rows="3">{{ old('clinical_notes', $certificate->clinical_notes ?? '') }}</textarea>
                </div>
                <div class="mt-4">
                    <label class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Declaración</label>
                    <textarea name="declaration_text" class="mt-2 w-full rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-200" rows="3">{{ old('declaration_text', $declaration ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-gradient-to-br from-indigo-600 to-teal-500 text-white rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-bold">Acciones rápidas</h3>
                <p class="text-sm text-indigo-100">Guarda como borrador o continúa editando.</p>
                <div class="mt-4 space-y-2">
                    <button type="submit" class="w-full bg-white text-indigo-700 font-semibold rounded-xl py-2 shadow hover:shadow-md transition">Guardar</button>
                    <button type="submit" name="save_and_continue" value="1" class="w-full bg-indigo-800/30 border border-white/30 text-white font-semibold rounded-xl py-2 hover:bg-indigo-800/50 transition">Guardar y seguir</button>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <h4 class="text-sm font-semibold text-slate-900">Recordatorios</h4>
                <ul class="mt-3 text-sm text-slate-600 space-y-2 list-disc list-inside">
                    <li>Adjunta documentos desde la vista de edición una vez guardado.</li>
                    <li>Los datos del tutor y mascota se fijan cuando vienes desde un paciente.</li>
                    <li>Selecciona un médico para completar nombre y licencia automáticamente.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('travelCertificateForm', (initial) => ({
            state: {
                type: initial.type,
                language: initial.language,
                lockOwner: initial.lockOwner,
                originDepartment: initial.originDepartment,
                destinationDepartment: initial.destinationDepartment,
                originMunicipality: initial.originMunicipality,
                destinationMunicipality: initial.destinationMunicipality,
                originMunicipalities: [],
                destinationMunicipalities: [],
                vets: initial.vets,
                selectedVet: initial.defaultVetId ?? null,
                vetName: initial.vetName,
                vetLicense: initial.vetLicense,
            },
            init() {
                if (!this.state.selectedVet && this.state.vets.length) {
                    this.state.selectedVet = this.state.vets[0].id;
                }
                this.applyVet();
                if (this.state.originDepartment) {
                    this.loadMunicipalities('origin', this.state.originDepartment, this.state.originMunicipality);
                }
                if (this.state.destinationDepartment) {
                    this.loadMunicipalities('destination', this.state.destinationDepartment, this.state.destinationMunicipality);
                }
            },
            applyVet() {
                const vet = this.state.vets.find(v => Number(v.id) === Number(this.state.selectedVet));
                if (vet) {
                    this.state.vetName = vet.name;
                    this.state.vetLicense = vet.license ?? '';
                }
            },
            async loadMunicipalities(kind, departmentId = null, selected = null) {
                const deptId = departmentId || (kind === 'origin' ? this.state.originDepartment : this.state.destinationDepartment);
                if (!deptId) {
                    if (kind === 'origin') this.state.originMunicipalities = [];
                    else this.state.destinationMunicipalities = [];
                    return;
                }
                try {
                    const response = await fetch(`/geo/departments/${deptId}/municipalities`);
                    const data = await response.json();
                    if (kind === 'origin') {
                        this.state.originMunicipalities = data;
                        if (selected) this.state.originMunicipality = selected;
                    } else {
                        this.state.destinationMunicipalities = data;
                        if (selected) this.state.destinationMunicipality = selected;
                    }
                } catch (error) {
                    console.error('No se pudieron cargar los municipios', error);
                }
            },
        }));
    });
</script>
@endpush
