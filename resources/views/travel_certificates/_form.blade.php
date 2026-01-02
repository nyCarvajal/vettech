@csrf
<div class="space-y-4" x-data="{ type: '{{ old('type', $certificate->type ?? 'national_co') }}' }">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium">Tipo</label>
            <select name="type" x-model="type" class="w-full border rounded p-2">
                <option value="national_co">Nacional (CO)</option>
                <option value="international">Internacional</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium">Idioma</label>
            <select name="language" class="w-full border rounded p-2">
                <option value="es" @selected(old('language', $certificate->language ?? 'es')==='es')>ES</option>
                <option value="en" @selected(old('language', $certificate->language ?? '')==='en')>EN</option>
                <option value="es_en" @selected(old('language', $certificate->language ?? '')==='es_en')>ES+EN</option>
            </select>
        </div>
    </div>
    <h3 class="text-lg font-semibold">Clínica y MV</h3>
    <div class="grid grid-cols-2 gap-4">
        <input type="text" name="clinic_name" placeholder="Clínica" value="{{ old('clinic_name', $certificate->clinic_name ?? $default['name'] ?? '') }}" class="border rounded p-2">
        <input type="text" name="clinic_nit" placeholder="NIT" value="{{ old('clinic_nit', $certificate->clinic_nit ?? $default['nit'] ?? '') }}" class="border rounded p-2">
        <input type="text" name="clinic_address" placeholder="Dirección" value="{{ old('clinic_address', $certificate->clinic_address ?? $default['address'] ?? '') }}" class="border rounded p-2">
        <input type="text" name="clinic_phone" placeholder="Teléfono" value="{{ old('clinic_phone', $certificate->clinic_phone ?? $default['phone'] ?? '') }}" class="border rounded p-2">
        <input type="text" name="clinic_city" placeholder="Ciudad" value="{{ old('clinic_city', $certificate->clinic_city ?? $default['city'] ?? '') }}" class="border rounded p-2">
        <input type="text" name="vet_name" placeholder="Médico Veterinario" value="{{ old('vet_name', $certificate->vet_name ?? $default['vet_name'] ?? '') }}" class="border rounded p-2">
        <input type="text" name="vet_license" placeholder="Licencia" value="{{ old('vet_license', $certificate->vet_license ?? $default['vet_license'] ?? '') }}" class="border rounded p-2">
    </div>

    <h3 class="text-lg font-semibold">Tutor</h3>
    <div class="grid grid-cols-2 gap-4">
        <input type="text" name="owner_name" placeholder="Nombre" value="{{ old('owner_name', $certificate->owner_name ?? '') }}" class="border rounded p-2">
        <input type="text" name="owner_document_number" placeholder="Documento" value="{{ old('owner_document_number', $certificate->owner_document_number ?? '') }}" class="border rounded p-2">
        <input type="text" name="owner_phone" placeholder="Teléfono" value="{{ old('owner_phone', $certificate->owner_phone ?? '') }}" class="border rounded p-2">
        <input type="email" name="owner_email" placeholder="Email" value="{{ old('owner_email', $certificate->owner_email ?? '') }}" class="border rounded p-2">
        <input type="text" name="owner_address" placeholder="Dirección" value="{{ old('owner_address', $certificate->owner_address ?? '') }}" class="border rounded p-2">
        <input type="text" name="owner_city" placeholder="Ciudad" value="{{ old('owner_city', $certificate->owner_city ?? '') }}" class="border rounded p-2">
    </div>

    <h3 class="text-lg font-semibold">Mascota</h3>
    <div class="grid grid-cols-2 gap-4">
        <input type="text" name="pet_name" placeholder="Nombre" value="{{ old('pet_name', $certificate->pet_name ?? '') }}" class="border rounded p-2">
        <input type="text" name="pet_species" placeholder="Especie" value="{{ old('pet_species', $certificate->pet_species ?? '') }}" class="border rounded p-2">
        <input type="text" name="pet_breed" placeholder="Raza" value="{{ old('pet_breed', $certificate->pet_breed ?? '') }}" class="border rounded p-2">
        <input type="text" name="pet_sex" placeholder="Sexo" value="{{ old('pet_sex', $certificate->pet_sex ?? '') }}" class="border rounded p-2">
        <input type="number" name="pet_age_months" placeholder="Edad (meses)" value="{{ old('pet_age_months', $certificate->pet_age_months ?? '') }}" class="border rounded p-2">
        <input type="number" step="0.01" name="pet_weight_kg" placeholder="Peso (kg)" value="{{ old('pet_weight_kg', $certificate->pet_weight_kg ?? '') }}" class="border rounded p-2">
        <input type="text" name="pet_color" placeholder="Color" value="{{ old('pet_color', $certificate->pet_color ?? '') }}" class="border rounded p-2">
        <input type="text" name="pet_microchip" placeholder="Microchip" value="{{ old('pet_microchip', $certificate->pet_microchip ?? '') }}" class="border rounded p-2">
    </div>

    <h3 class="text-lg font-semibold">Viaje</h3>
    <div class="grid grid-cols-2 gap-4">
        <input type="date" name="travel_departure_date" value="{{ old('travel_departure_date', optional($certificate->travel_departure_date ?? null)->format('Y-m-d')) }}" class="border rounded p-2">
        <input type="time" name="travel_departure_time" value="{{ old('travel_departure_time', optional($certificate->travel_departure_time ?? null)->format('H:i')) }}" class="border rounded p-2">
        <select name="transport_type" class="border rounded p-2">
            <option value="">Medio</option>
            <option value="air" @selected(old('transport_type', $certificate->transport_type ?? '')==='air')>Aéreo</option>
            <option value="land" @selected(old('transport_type', $certificate->transport_type ?? '')==='land')>Terrestre</option>
            <option value="other" @selected(old('transport_type', $certificate->transport_type ?? '')==='other')>Otro</option>
        </select>
        <input type="text" name="transport_company" placeholder="Empresa" value="{{ old('transport_company', $certificate->transport_company ?? '') }}" class="border rounded p-2">
        <input type="text" name="flight_number" placeholder="Vuelo" value="{{ old('flight_number', $certificate->flight_number ?? '') }}" class="border rounded p-2">
    </div>

    <div class="grid grid-cols-2 gap-4" x-show="type === 'national_co'">
        <div>
            <label class="block text-sm font-medium">Origen - Departamento</label>
            <select name="origin_department_id" class="border rounded p-2 w-full">
                <option value="">Seleccione</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}" @selected(old('origin_department_id', $certificate->origin_department_id ?? '')==$department->id)>{{ $department->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium">Origen - Municipio</label>
            <input type="text" name="origin_municipality_id" value="{{ old('origin_municipality_id', $certificate->origin_municipality_id ?? '') }}" class="border rounded p-2 w-full" placeholder="Municipio ID">
        </div>
        <div>
            <label class="block text-sm font-medium">Destino - Departamento</label>
            <select name="destination_department_id" class="border rounded p-2 w-full">
                <option value="">Seleccione</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}" @selected(old('destination_department_id', $certificate->destination_department_id ?? '')==$department->id)>{{ $department->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium">Destino - Municipio</label>
            <input type="text" name="destination_municipality_id" value="{{ old('destination_municipality_id', $certificate->destination_municipality_id ?? '') }}" class="border rounded p-2 w-full" placeholder="Municipio ID">
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4" x-show="type === 'international'">
        <div>
            <label class="block text-sm font-medium">Origen - País</label>
            <select name="origin_country_code" class="border rounded p-2 w-full">
                <option value="">Seleccione</option>
                @foreach($countries as $country)
                    <option value="{{ $country->code2 }}" @selected(old('origin_country_code', $certificate->origin_country_code ?? '')==$country->code2)>{{ $country->name_es }}</option>
                @endforeach
            </select>
        </div>
        <input type="text" name="origin_city" placeholder="Ciudad origen" value="{{ old('origin_city', $certificate->origin_city ?? '') }}" class="border rounded p-2">
        <div>
            <label class="block text-sm font-medium">Destino - País</label>
            <select name="destination_country_code" class="border rounded p-2 w-full">
                <option value="">Seleccione</option>
                @foreach($countries as $country)
                    <option value="{{ $country->code2 }}" @selected(old('destination_country_code', $certificate->destination_country_code ?? '')==$country->code2)>{{ $country->name_es }}</option>
                @endforeach
            </select>
        </div>
        <input type="text" name="destination_city" placeholder="Ciudad destino" value="{{ old('destination_city', $certificate->destination_city ?? '') }}" class="border rounded p-2">
    </div>

    <h3 class="text-lg font-semibold">Examen clínico</h3>
    <div class="grid grid-cols-2 gap-4">
        <input type="datetime-local" name="clinical_exam_at" value="{{ old('clinical_exam_at', optional($certificate->clinical_exam_at ?? now())->format('Y-m-d\TH:i')) }}" class="border rounded p-2">
        <textarea name="clinical_notes" class="border rounded p-2" placeholder="Notas">{{ old('clinical_notes', $certificate->clinical_notes ?? '') }}</textarea>
        <label class="inline-flex items-center space-x-2">
            <input type="checkbox" name="fit_for_travel" value="1" @checked(old('fit_for_travel', $certificate->fit_for_travel ?? true))>
            <span>Apto para viajar</span>
        </label>
    </div>
    <div>
        <label class="block text-sm font-medium">Declaración</label>
        <textarea name="declaration_text" class="border rounded p-2 w-full" rows="3">{{ old('declaration_text', $declaration ?? '') }}</textarea>
    </div>
</div>
