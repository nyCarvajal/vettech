@csrf
<div class="mb-3">
    <label for="name" class="form-label">Nombre</label>
    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $raza->name ?? '') }}" required maxlength="150">
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label for="species_id" class="form-label">Especie</label>
    <select name="species_id" id="species_id" class="form-select @error('species_id') is-invalid @enderror" required>
        <option value="">Selecciona una especie</option>
        @foreach ($species as $especie)
            <option value="{{ $especie->id }}" @selected(old('species_id', $raza->species_id ?? null) == $especie->id)>{{ $especie->name }}</option>
        @endforeach
    </select>
    @error('species_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
