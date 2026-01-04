@once
    @push('scripts')
        @vite('resources/js/consents/template-editor.js')
    @endpush
    @push('styles')
        <style>
            .ql-editor .placeholder-chip {
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                border: 1px solid #dbeafe;
                background: linear-gradient(90deg, #eef2ff 0%, #e0f2fe 100%);
                color: #1f2937;
                border-radius: 9999px;
                padding: 0.3rem 0.75rem;
                font-size: 0.78rem;
                font-weight: 600;
                letter-spacing: -0.01em;
                white-space: nowrap;
                box-shadow: 0 1px 0 rgba(59, 130, 246, 0.08);
            }

            .ql-editor .placeholder-chip .placeholder-chip-key {
                color: #1d4ed8;
                font-weight: 700;
                background-color: rgba(59, 130, 246, 0.08);
                padding: 0.15rem 0.45rem;
                border-radius: 9999px;
                text-transform: uppercase;
                font-size: 0.7rem;
            }

            .ql-editor .placeholder-chip .placeholder-chip-label {
                color: #111827;
                font-weight: 600;
            }

            .signer-card {
                position: relative;
                overflow: hidden;
                border: 1px solid #e5e7eb;
                transition: all 0.18s ease-in-out;
            }

            .signer-card::before {
                content: '';
                position: absolute;
                inset: 0;
                background: linear-gradient(135deg, rgba(99, 102, 241, 0.08), rgba(56, 189, 248, 0.08));
                opacity: 0;
                transition: opacity 0.18s ease-in-out;
            }

            .signer-card:hover {
                border-color: #c7d2fe;
                box-shadow: 0 6px 14px rgba(99, 102, 241, 0.12);
                transform: translateY(-1px);
            }

            .signer-card:hover::before {
                opacity: 1;
            }

            .signer-chip-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 2.25rem;
                height: 2.25rem;
                border-radius: 9999px;
                background: linear-gradient(135deg, #6366f1, #22d3ee);
                color: #fff;
                font-weight: 800;
                letter-spacing: -0.02em;
                box-shadow: 0 4px 10px rgba(79, 70, 229, 0.25);
            }
        </style>
    @endpush
@endonce

@php($requiredSigners = ['owner' => 'Tutor', 'vet' => 'Veterinario', 'witness' => 'Testigo'])
@php($bodyHtml = old('body_html', $template->body_html))
@php($groupedPlaceholders = [
    'Datos del tutor' => array_filter($placeholders, fn ($_, $key) => str_starts_with($key, 'owner.'), ARRAY_FILTER_USE_BOTH),
    'Datos del paciente' => array_filter($placeholders, fn ($_, $key) => str_starts_with($key, 'pet.'), ARRAY_FILTER_USE_BOTH),
    'Información de la clínica' => array_filter($placeholders, fn ($_, $key) => str_starts_with($key, 'clinic.'), ARRAY_FILTER_USE_BOTH),
    'Profesional tratante' => array_filter($placeholders, fn ($_, $key) => str_starts_with($key, 'vet.'), ARRAY_FILTER_USE_BOTH),
    'Fecha y hora' => array_filter($placeholders, fn ($_, $key) => str_starts_with($key, 'now.'), ARRAY_FILTER_USE_BOTH),
    'Campos personalizados' => array_filter($placeholders, fn ($_, $key) => str_starts_with($key, 'custom.'), ARRAY_FILTER_USE_BOTH),
])
<div class="space-y-6" data-consent-template-editor data-placeholders='@json($placeholders)'>
    <div>
        <label class="block text-sm font-medium">Nombre</label>
        <input type="text" name="name" value="{{ old('name', $template->name) }}" class="mt-1 w-full border rounded px-3 py-2">
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium">Categoría</label>
            <input type="text" name="category" value="{{ old('category', $template->category) }}" class="mt-1 w-full border rounded px-3 py-2">
        </div>
        <div class="flex items-center space-x-2 mt-6">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
            <span>Activo</span>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <label class="flex items-center space-x-2"><input type="checkbox" name="requires_owner" value="1" {{ old('requires_owner', $template->requires_owner ?? true) ? 'checked' : '' }}> <span>Requiere tutor</span></label>
        <label class="flex items-center space-x-2"><input type="checkbox" name="requires_pet" value="1" {{ old('requires_pet', $template->requires_pet ?? true) ? 'checked' : '' }}> <span>Requiere paciente</span></label>
    </div>
    <div>
        <label class="block text-sm font-medium">Cuerpo (HTML)</label>
        <div class="mt-1 space-y-2">
            <input type="hidden" name="body_html" value="{{ $bodyHtml }}" data-editor-input>
            <div data-editor class="bg-white border rounded min-h-[240px] p-3">
                {!! $bodyHtml !!}
            </div>
            <p class="text-xs text-gray-500">Usa el editor para personalizar el contenido. Las etiquetas se reemplazarán con los datos del paciente cuando se genere el consentimiento.</p>
        </div>
    </div>
    <div class="bg-gray-50 border rounded p-4 space-y-4">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="font-semibold text-base">Etiquetas disponibles</p>
                <p class="text-sm text-gray-600">Selecciona y activa solo las etiquetas que necesites. Haz clic en “Insertar” para agregarlas al cuerpo del consentimiento.</p>
            </div>
            <div class="text-right text-xs text-gray-500">Todas las etiquetas están en español y mostrarán datos reales al generar el documento.</div>
        </div>
        <div class="space-y-5">
            @foreach($groupedPlaceholders as $groupLabel => $groupItems)
                @if(!empty($groupItems))
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <p class="font-semibold text-sm text-gray-800">{{ $groupLabel }}</p>
                            <p class="text-xs text-gray-500">Inserta etiquetas con un clic</p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($groupItems as $key => $meta)
                                <div class="rounded-xl border bg-white p-4 shadow-sm space-y-3">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="space-y-1">
                                            <p class="font-semibold text-sm text-gray-900">{{ $meta['label'] }}</p>
                                            @if(!empty($meta['example']))
                                                <p class="text-xs text-gray-500">Ejemplo: {{ $meta['example'] }}</p>
                                            @endif
                                            <p class="text-[11px] text-indigo-700 font-semibold flex items-center gap-1">
                                                <span class="inline-flex items-center rounded-full bg-indigo-50 px-2 py-0.5 border border-indigo-100">Etiqueta</span>
                                                <span class="text-gray-600">{{ $key }}</span>
                                            </p>
                                        </div>
                                        <button type="button" class="inline-flex items-center gap-2 rounded-full bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 border border-indigo-100 hover:bg-indigo-100 shadow-sm" data-placeholder-key="{{ $key }}" data-placeholder-label="{{ $meta['label'] }}">
                                            <span>Insertar en el cuerpo</span>
                                        </button>
                                    </div>
                                    <label class="flex items-center gap-2 text-xs text-gray-700">
                                        <input type="checkbox" name="allowed_placeholders[]" value="{{ $key }}" class="h-4 w-4 text-indigo-600 rounded border-gray-300" {{ in_array($key, old('allowed_placeholders', $template->allowed_placeholders ?? [])) ? 'checked' : '' }}>
                                        <span>Activar esta etiqueta para el consentimiento</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
    <div class="space-y-3">
        <p class="font-semibold text-base">Firmantes requeridos</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            @foreach($requiredSigners as $value => $label)
                <label class="signer-card block rounded-xl bg-white p-4 shadow-sm hover:border-indigo-200 transition flex items-center gap-3">
                    <span class="signer-chip-icon">{{ mb_substr($label, 0, 1) }}</span>
                    <input type="checkbox" name="required_signers[]" value="{{ $value }}" class="h-5 w-5 text-indigo-600 rounded border-gray-300 relative z-10" {{ in_array($value, old('required_signers', $template->required_signers ?? [])) ? 'checked' : '' }}>
                    <div class="flex flex-col relative z-10">
                        <span class="font-semibold text-sm text-gray-900">{{ $label }}</span>
                        <span class="text-xs text-gray-500">Activa esta casilla si {{ strtolower($label) }} debe firmar el consentimiento</span>
                    </div>
                </label>
            @endforeach
        </div>
    </div>
    <div class="flex justify-end pt-2">
        <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1">
            Guardar consentimiento
        </button>
    </div>
</div>
