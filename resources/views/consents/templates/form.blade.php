@php($requiredSigners = ['owner' => 'Tutor', 'vet' => 'Veterinario', 'witness' => 'Testigo'])
<div class="space-y-4" x-data="{insert(tag){const textarea=$refs.body; const start=textarea.selectionStart; const end=textarea.selectionEnd; const text=textarea.value; textarea.value=text.slice(0,start)+tag+text.slice(end); textarea.focus(); textarea.selectionStart=textarea.selectionEnd=start+tag.length;}}">
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
        <textarea x-ref="body" name="body_html" rows="10" class="mt-1 w-full border rounded px-3 py-2">{{ old('body_html', $template->body_html) }}</textarea>
    </div>
    <div class="bg-gray-50 border rounded p-4">
        <p class="font-semibold mb-2">Etiquetas disponibles</p>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
            @foreach($placeholders as $key => $meta)
            @php($placeholderTag = '{{' . $key . '}}')
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="allowed_placeholders[]" value="{{ $key }}" {{ in_array($key, old('allowed_placeholders', $template->allowed_placeholders ?? [])) ? 'checked' : '' }}>
                <button type="button" class="text-indigo-600 underline" @click="insert('{{ $placeholderTag }}')">{{ $key }}</button>
            </label>
            @endforeach
        </div>
    </div>
    <div>
        <p class="font-semibold mb-2">Firmantes requeridos</p>
        <div class="flex space-x-4">
            @foreach($requiredSigners as $value => $label)
                <label class="flex items-center space-x-2"><input type="checkbox" name="required_signers[]" value="{{ $value }}" {{ in_array($value, old('required_signers', $template->required_signers ?? [])) ? 'checked' : '' }}> <span>{{ $label }}</span></label>
            @endforeach
        </div>
    </div>
</div>
