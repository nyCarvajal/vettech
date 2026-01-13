@csrf
<div class="mb-3">
    <label for="name" class="form-label">Nombre</label>
    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $especie->name ?? '') }}" required maxlength="150">
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
