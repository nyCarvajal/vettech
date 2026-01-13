@csrf
<div class="mb-3">
    <label for="nombre" class="form-label">Nombre</label>
    <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $tipocita->nombre ?? '') }}" required maxlength="150">
    @error('nombre')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label for="descripcion" class="form-label">Descripción</label>
    <input type="text" name="descripcion" id="descripcion" class="form-control @error('descripcion') is-invalid @enderror" value="{{ old('descripcion', $tipocita->descripcion ?? '') }}" maxlength="255">
    @error('descripcion')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label for="duracion" class="form-label">Duración (minutos)</label>
    <input type="number" name="duracion" id="duracion" class="form-control @error('duracion') is-invalid @enderror" value="{{ old('duracion', $tipocita->duracion ?? '') }}" min="0">
    @error('duracion')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
