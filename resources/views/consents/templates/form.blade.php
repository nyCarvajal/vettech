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
                border: 1px solid #e0e7ff;
                background-color: #eef2ff;
                color: #3730a3;
                border-radius: 9999px;
                padding: 0.25rem 0.6rem;
                font-size: 0.75rem;
                font-weight: 600;
                white-space: nowrap;
            }

            .ql-editor .placeholder-chip .placeholder-chip-key {
                color: #4b5563;
                font-weight: 500;
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
                                <div class="rounded-lg border bg-white p-4 shadow-sm space-y-2">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="space-y-1">
                                            <p class="font-semibold text-sm text-gray-900">{{ $meta['label'] }}</p>
                                            @if(!empty($meta['example']))
                                                <p class="text-xs text-gray-500">Ejemplo: {{ $meta['example'] }}</p>
                                            @endif
                                            <p class="text-[11px] text-gray-400">Llave: {{ $key }}</p>
                                        </div>
                                        <button type="button" class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 border border-indigo-100 hover:bg-indigo-100" data-placeholder-key="{{ $key }}" data-placeholder-label="{{ $meta['label'] }}">
                                            Insertar
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
                <label class="block rounded-lg border bg-white p-4 shadow-sm hover:border-indigo-200 transition flex items-center gap-3">
                    <input type="checkbox" name="required_signers[]" value="{{ $value }}" class="h-5 w-5 text-indigo-600 rounded border-gray-300" {{ in_array($value, old('required_signers', $template->required_signers ?? [])) ? 'checked' : '' }}>
                    <div class="flex flex-col">
                        <span class="font-semibold text-sm text-gray-900">{{ $label }}</span>
                        <span class="text-xs text-gray-500">Marcar si este rol debe firmar el consentimiento</span>
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
