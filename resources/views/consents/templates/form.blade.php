@php($requiredSigners = ['owner' => 'Tutor', 'vet' => 'Veterinario', 'witness' => 'Testigo'])
<div class="space-y-6" x-data="{insert(tag){const textarea=$refs.body; const start=textarea.selectionStart; const end=textarea.selectionEnd; const text=textarea.value; textarea.value=text.slice(0,start)+tag+text.slice(end); textarea.focus(); textarea.selectionStart=textarea.selectionEnd=start+tag.length;}}">
    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Nombre</label>
                    <input type="text" name="name" value="{{ old('name', $template->name) }}" class="mt-1 w-full border border-slate-200 rounded-lg px-3 py-2 focus:border-indigo-400 focus:ring-indigo-200">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Categoría</label>
                    <input type="text" name="category" value="{{ old('category', $template->category) }}" class="mt-1 w-full border border-slate-200 rounded-lg px-3 py-2 focus:border-indigo-400 focus:ring-indigo-200" placeholder="Cirugía, anestesia, viaje...">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <label class="flex items-center space-x-2 text-sm text-slate-700"><input type="checkbox" name="requires_owner" value="1" {{ old('requires_owner', $template->requires_owner ?? true) ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500 rounded"> <span>Requiere tutor</span></label>
                <label class="flex items-center space-x-2 text-sm text-slate-700"><input type="checkbox" name="requires_pet" value="1" {{ old('requires_pet', $template->requires_pet ?? true) ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500 rounded"> <span>Requiere paciente</span></label>
            </div>
            <div class="flex items-center space-x-3 bg-slate-50 border border-slate-200 rounded-xl px-4 py-3">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $template->is_active) ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500 rounded">
                <div>
                    <p class="text-sm font-semibold text-slate-800">Activo</p>
                    <p class="text-xs text-slate-500">Se mostrará en la lista de plantillas disponibles.</p>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700">Cuerpo (HTML)</label>
                <p class="text-xs text-slate-500">Escribe el consentimiento. Usa las etiquetas rápidas para autocompletar datos.</p>
                <textarea x-ref="body" name="body_html" rows="12" class="mt-2 w-full border border-slate-200 rounded-xl px-3 py-3 focus:border-indigo-400 focus:ring-indigo-200">{{ old('body_html', $template->body_html) }}</textarea>
            </div>
        </div>
        <div class="space-y-3">
            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 shadow-inner">
                <p class="text-sm font-semibold text-emerald-800 flex items-center justify-between">Etiquetas rápidas <span class="text-xs font-normal text-emerald-600">inserta a la derecha</span></p>
                <p class="text-xs text-emerald-700 mb-3">Click en la menta para insertar en el editor.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach($placeholders as $key => $meta)
                        @php($placeholderTag = '{{' . $key . '}}')
                        @php($label = preg_replace('/\s+/', ' ', ucfirst(trim(str_replace(['del', 'de la', 'de'], '', strtolower($meta['label'] ?? $key))))))
                        <label class="flex items-center justify-between space-x-2 bg-white border border-emerald-100 rounded-xl px-3 py-2 shadow-sm">
                            <div class="space-y-1">
                                <p class="text-sm font-semibold text-slate-700 leading-tight">{{ trim($label) }}</p>
                                <p class="text-[11px] text-slate-400 tracking-wide">{{ $placeholderTag }}</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <input type="checkbox" name="allowed_placeholders[]" value="{{ $key }}" {{ in_array($key, old('allowed_placeholders', $template->allowed_placeholders ?? [])) ? 'checked' : '' }} class="text-emerald-500 focus:ring-emerald-500 rounded">
                                <button type="button" class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-emerald-500 text-white text-xs font-bold shadow" @click="insert('{{ $placeholderTag }}')">+</button>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
        <p class="font-semibold mb-2 text-slate-800">Firmantes requeridos</p>
        <div class="flex flex-wrap gap-4">
            @foreach($requiredSigners as $value => $label)
                <label class="flex items-center space-x-2 text-sm text-slate-700"><input type="checkbox" name="required_signers[]" value="{{ $value }}" {{ in_array($value, old('required_signers', $template->required_signers ?? [])) ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500 rounded"> <span>{{ $label }}</span></label>
            @endforeach
        </div>
    </div>
</div>
