@csrf
@php
    $patientSnapshotData = old('patient_snapshot')
        ? (is_string(old('patient_snapshot')) ? json_decode(old('patient_snapshot'), true) ?? [] : old('patient_snapshot'))
        : ($patientSnapshot ?? ($procedure->patient_snapshot ?? []));
    $ownerSnapshotData = old('owner_snapshot')
        ? (is_string(old('owner_snapshot')) ? json_decode(old('owner_snapshot'), true) ?? [] : old('owner_snapshot'))
        : ($ownerSnapshot ?? ($procedure->owner_snapshot ?? []));
    $responsibleUsersList = $responsibleUsers ?? collect();
    $defaultResponsibleName = $defaultResponsibleName ?? null;
    $defaultResponsibleLicense = $defaultResponsibleLicense ?? null;
    $selectedResponsibleName = old('responsible_vet_name', $procedure->responsible_vet_name ?: $defaultResponsibleName);
    $initialLicense = old('responsible_vet_license', $procedure->responsible_vet_license ?: $defaultResponsibleLicense);
@endphp

<input type="hidden" name="patient_id" value="{{ old('patient_id', $procedure->patient_id ?? optional($patient ?? null)->id) }}">
<input type="hidden" name="owner_id" value="{{ old('owner_id', $procedure->owner_id ?? optional(optional($patient ?? null)->owner)->id) }}">
<input type="hidden" name="patient_snapshot" value='@json($patientSnapshotData, JSON_UNESCAPED_UNICODE)'>
<input type="hidden" name="owner_snapshot" value='@json($ownerSnapshotData, JSON_UNESCAPED_UNICODE)'>

<div
    x-data="{ section: 'basics', medications: @json(old('anesthesia_medications', $procedure->anesthesiaMedications->toArray() ?? [])) }"
    class="space-y-6"
    data-procedure-form
    data-active-section="basics"
>
    <div class="flex space-x-2">
        <button
            type="button"
            data-section-button="basics"
            @click="section='basics'"
            :class="section==='basics' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-800'"
            class="px-3 py-2 rounded bg-gray-200 text-gray-800"
        >Datos básicos</button>
        <button
            type="button"
            data-section-button="schedule"
            @click="section='schedule'"
            :class="section==='schedule' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-800'"
            class="px-3 py-2 rounded bg-gray-200 text-gray-800"
        >Programación</button>
        <button
            type="button"
            data-section-button="anesthesia"
            @click="section='anesthesia'"
            :class="section==='anesthesia' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-800'"
            class="px-3 py-2 rounded bg-gray-200 text-gray-800"
        >Anestesia</button>
        <button
            type="button"
            data-section-button="notes"
            @click="section='notes'"
            :class="section==='notes' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-800'"
            class="px-3 py-2 rounded bg-gray-200 text-gray-800"
        >Notas</button>
        <button
            type="button"
            data-section-button="consent"
            @click="section='consent'"
            :class="section==='consent' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-800'"
            class="px-3 py-2 rounded bg-gray-200 text-gray-800"
        >Consentimiento</button>
    </div>

    <div x-show="section==='basics'" data-section="basics" class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white p-4 rounded shadow">
        <div>
            <label class="block text-sm font-medium">Tipo *</label>
            <select name="type" class="input input-bordered w-full" required>
                <option value="surgery" @selected(old('type', $procedure->type)==='surgery')>Cirugía</option>
                <option value="procedure" @selected(old('type', $procedure->type)==='procedure')>Procedimiento</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium">Nombre *</label>
            <input type="text" name="name" value="{{ old('name', $procedure->name) }}" class="input input-bordered w-full" required>
        </div>
        <div>
            <label class="block text-sm font-medium">Categoría</label>
            <input type="text" name="category" value="{{ old('category', $procedure->category) }}" class="input input-bordered w-full">
        </div>
        <div>
            <label class="block text-sm font-medium">Estado *</label>
            <select name="status" class="input input-bordered w-full" required>
                @foreach(['scheduled'=>'Programado','in_progress'=>'En curso','completed'=>'Completado','canceled'=>'Cancelado'] as $value=>$label)
                <option value="{{ $value }}" @selected(old('status', $procedure->status)===$value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-semibold text-gray-800">Paciente</h3>
                    <span class="text-xs px-2 py-1 rounded bg-indigo-100 text-indigo-800">Solo lectura</span>
                </div>
                @if(! empty($patientSnapshotData))
                    <dl class="text-sm text-gray-700 space-y-1">
                        <div class="grid grid-cols-3 gap-1">
                            <dt class="font-medium">Nombre</dt>
                            <dd class="col-span-2">{{ $patientSnapshotData['name'] ?? '—' }}</dd>
                        </div>
                        <div class="grid grid-cols-3 gap-1">
                            <dt class="font-medium">Especie</dt>
                            <dd class="col-span-2">{{ $patientSnapshotData['species'] ?? '—' }}</dd>
                        </div>
                        <div class="grid grid-cols-3 gap-1">
                            <dt class="font-medium">Raza</dt>
                            <dd class="col-span-2">{{ $patientSnapshotData['breed'] ?? '—' }}</dd>
                        </div>
                        <div class="grid grid-cols-3 gap-1">
                            <dt class="font-medium">Sexo</dt>
                            <dd class="col-span-2">{{ $patientSnapshotData['sex'] ?? '—' }}</dd>
                        </div>
                        <div class="grid grid-cols-3 gap-1">
                            <dt class="font-medium">Edad</dt>
                            <dd class="col-span-2">{{ $patientSnapshotData['age'] ?? '—' }}</dd>
                        </div>
                        <div class="grid grid-cols-3 gap-1">
                            <dt class="font-medium">Peso</dt>
                            <dd class="col-span-2">{{ $patientSnapshotData['weight'] ?? '—' }}</dd>
                        </div>
                        <div class="grid grid-cols-3 gap-1">
                            <dt class="font-medium">Microchip</dt>
                            <dd class="col-span-2">{{ $patientSnapshotData['microchip'] ?? '—' }}</dd>
                        </div>
                    </dl>
                @else
                    <p class="text-sm text-gray-500">No se encontraron datos del paciente.</p>
                @endif
            </div>
            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-semibold text-gray-800">Tutor</h3>
                    <span class="text-xs px-2 py-1 rounded bg-indigo-100 text-indigo-800">Solo lectura</span>
                </div>
                @if(! empty($ownerSnapshotData))
                    <dl class="text-sm text-gray-700 space-y-1">
                        <div class="grid grid-cols-3 gap-1">
                            <dt class="font-medium">Nombre</dt>
                            <dd class="col-span-2">{{ $ownerSnapshotData['name'] ?? '—' }}</dd>
                        </div>
                        <div class="grid grid-cols-3 gap-1">
                            <dt class="font-medium">Documento</dt>
                            <dd class="col-span-2">{{ $ownerSnapshotData['document'] ?? '—' }}</dd>
                        </div>
                        <div class="grid grid-cols-3 gap-1">
                            <dt class="font-medium">Teléfono</dt>
                            <dd class="col-span-2">{{ $ownerSnapshotData['phone'] ?? '—' }}</dd>
                        </div>
                        <div class="grid grid-cols-3 gap-1">
                            <dt class="font-medium">Correo</dt>
                            <dd class="col-span-2">{{ $ownerSnapshotData['email'] ?? '—' }}</dd>
                        </div>
                        <div class="grid grid-cols-3 gap-1">
                            <dt class="font-medium">Dirección</dt>
                            <dd class="col-span-2">{{ $ownerSnapshotData['address'] ?? '—' }}</dd>
                        </div>
                    </dl>
                @else
                    <p class="text-sm text-gray-500">No se encontraron datos del tutor.</p>
                @endif
            </div>
        </div>
    </div>

    <div x-show="section==='schedule'" data-section="schedule" class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-white p-4 rounded shadow">
        <div>
            <label class="block text-sm font-medium">Programado para</label>
            <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at', optional($procedure->scheduled_at)->format('Y-m-d\TH:i')) }}" class="input input-bordered w-full">
        </div>
        <div>
            <label class="block text-sm font-medium">Inicio</label>
            <input type="datetime-local" name="started_at" value="{{ old('started_at', optional($procedure->started_at)->format('Y-m-d\TH:i')) }}" class="input input-bordered w-full">
        </div>
        <div>
            <label class="block text-sm font-medium">Fin</label>
            <input type="datetime-local" name="ended_at" value="{{ old('ended_at', optional($procedure->ended_at)->format('Y-m-d\TH:i')) }}" class="input input-bordered w-full">
        </div>
        <div>
            <label class="block text-sm font-medium">Ubicación</label>
            <input type="text" name="location" value="{{ old('location', $procedure->location) }}" class="input input-bordered w-full">
        </div>
        <div>
            <label class="block text-sm font-medium">Responsable</label>
            <select name="responsible_vet_name" class="input input-bordered w-full" data-responsible-select>
                @if($defaultResponsibleName)
                    <option value="{{ $defaultResponsibleName }}" data-license="{{ $defaultResponsibleLicense }}" @selected($selectedResponsibleName===$defaultResponsibleName)>
                        {{ $defaultResponsibleName }} (tú)
                    </option>
                @endif
                @foreach($responsibleUsersList as $user)
                    @php
                        $fullName = trim(($user->nombre ?? '') . ' ' . ($user->apellidos ?? ''));
                        $license = $user->firma_medica_texto ?? $user->numero_identificacion;
                    @endphp
                    @continue($defaultResponsibleName && $fullName === $defaultResponsibleName)
                    <option value="{{ $fullName }}" data-license="{{ $license }}" @selected($selectedResponsibleName===$fullName)>
                        {{ $fullName }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium">Licencia</label>
            <input type="text" name="responsible_vet_license" value="{{ $initialLicense }}" class="input input-bordered w-full" data-responsible-license>
        </div>
    </div>

    <div x-show="section==='anesthesia'" data-section="anesthesia" class="space-y-4 bg-white p-4 rounded shadow">
        <div>
            <label class="block text-sm font-medium">Plan anestésico</label>
            <textarea name="anesthesia_plan" rows="3" class="input input-bordered w-full">{{ old('anesthesia_plan', $procedure->anesthesia_plan) }}</textarea>
        </div>
        <div>
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-medium">Medicamentos</h3>
                <button type="button" class="text-indigo-600" @click="medications.push({drug_name:'',dose:'',dose_unit:'',route:'',frequency:'',notes:''})">Agregar</button>
            </div>
            <template x-for="(medication,index) in medications" :key="index">
                <div class="grid grid-cols-1 md:grid-cols-6 gap-2 mb-2 items-center">
                    <input class="input input-bordered" :name="`anesthesia_medications[${index}][drug_name]`" x-model="medication.drug_name" placeholder="Fármaco" required>
                    <input class="input input-bordered" :name="`anesthesia_medications[${index}][dose]`" x-model="medication.dose" placeholder="Dosis">
                    <input class="input input-bordered" :name="`anesthesia_medications[${index}][dose_unit]`" x-model="medication.dose_unit" placeholder="Unidad">
                    <input class="input input-bordered" :name="`anesthesia_medications[${index}][route]`" x-model="medication.route" placeholder="Vía">
                    <input class="input input-bordered" :name="`anesthesia_medications[${index}][frequency]`" x-model="medication.frequency" placeholder="Frecuencia">
                    <div class="flex items-center space-x-2">
                        <input class="input input-bordered w-full" :name="`anesthesia_medications[${index}][notes]`" x-model="medication.notes" placeholder="Notas">
                        <button type="button" class="text-red-600" @click="medications.splice(index,1)">X</button>
                    </div>
                </div>
            </template>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium">Monitoreo</label>
                <textarea name="anesthesia_monitoring" rows="3" class="input input-bordered w-full">{{ old('anesthesia_monitoring', $procedure->anesthesia_monitoring) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium">Notas de anestesia</label>
                <textarea name="anesthesia_notes" rows="3" class="input input-bordered w-full">{{ old('anesthesia_notes', $procedure->anesthesia_notes) }}</textarea>
            </div>
        </div>
    </div>

    <div x-show="section==='notes'" data-section="notes" class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white p-4 rounded shadow">
        <div>
            <label class="block text-sm font-medium">Preoperatorio</label>
            <textarea name="preop_notes" rows="3" class="input input-bordered w-full">{{ old('preop_notes', $procedure->preop_notes) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium">Intraoperatorio</label>
            <textarea name="intraop_notes" rows="3" class="input input-bordered w-full">{{ old('intraop_notes', $procedure->intraop_notes) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium">Postoperatorio</label>
            <textarea name="postop_notes" rows="3" class="input input-bordered w-full">{{ old('postop_notes', $procedure->postop_notes) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium">Complicaciones</label>
            <textarea name="complications" rows="3" class="input input-bordered w-full">{{ old('complications', $procedure->complications) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium">Manejo del dolor</label>
            <textarea name="pain_management" rows="3" class="input input-bordered w-full">{{ old('pain_management', $procedure->pain_management) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium">Observaciones</label>
            <textarea name="observations" rows="3" class="input input-bordered w-full">{{ old('observations', $procedure->observations) }}</textarea>
        </div>
    </div>

    <div x-show="section==='consent'" data-section="consent" class="bg-white p-4 rounded shadow space-y-4">
        <div>
            <label class="block text-sm font-medium">Consentimiento firmado</label>
            <input type="text" name="consent_document_id" value="{{ old('consent_document_id', $procedure->consent_document_id)}}" class="input input-bordered w-full" placeholder="ID de documento firmado">
            <p class="text-xs text-gray-500 mt-1">Ingresa el ID de un consentimiento firmado o usa los botones de la vista para generarlo.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-gray-700">
            <p>Vincula un consentimiento firmado existente para este paciente/tutor.</p>
            <p>También puedes generar uno nuevo desde plantilla y firmarlo sin salir.</p>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const wrapper = document.querySelector('[data-procedure-form]');
                if (!wrapper || wrapper.__x) return; // Alpine ya controla el formulario

                const buttons = Array.from(wrapper.querySelectorAll('[data-section-button]'));
                const sections = Array.from(wrapper.querySelectorAll('[data-section]'));
                if (!buttons.length || !sections.length) return;

                const toggleSection = (target) => {
                    buttons.forEach((btn) => {
                        const isActive = btn.dataset.sectionButton === target;
                        btn.classList.toggle('bg-indigo-600', isActive);
                        btn.classList.toggle('text-white', isActive);
                        btn.classList.toggle('bg-gray-200', !isActive);
                        btn.classList.toggle('text-gray-800', !isActive);
                    });

                    sections.forEach((section) => {
                        const isActive = section.dataset.section === target;
                        section.classList.toggle('hidden', !isActive);
                    });
                };

                const initial = wrapper.dataset.activeSection || buttons[0].dataset.sectionButton;
                toggleSection(initial);
                buttons.forEach((btn) => btn.addEventListener('click', () => toggleSection(btn.dataset.sectionButton)));

                const responsibleSelect = wrapper.querySelector('[data-responsible-select]');
                const licenseInput = wrapper.querySelector('[data-responsible-license]');

                const syncLicense = (force = false) => {
                    if (!responsibleSelect || !licenseInput) return;
                    const option = responsibleSelect.selectedOptions[0];
                    if (!option) return;

                    const license = option.dataset.license ?? '';
                    if (force || !licenseInput.value.trim()) {
                        licenseInput.value = license;
                    }
                };

                syncLicense(true);
                responsibleSelect?.addEventListener('change', () => syncLicense(true));
            });
        </script>
    @endpush
@endonce
