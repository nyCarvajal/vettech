@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Razón social *</label>
        <input type="text" name="razon_social" class="form-control" value="{{ old('razon_social', $supplier->razon_social ?? '') }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Tipo documento</label>
        <input type="text" name="tipo_documento" class="form-control" value="{{ old('tipo_documento', $supplier->tipo_documento ?? '') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Número documento</label>
        <input type="text" name="numero_documento" class="form-control" value="{{ old('numero_documento', $supplier->numero_documento ?? '') }}">
    </div>
    <div class="col-md-3"><label class="form-label">Teléfono</label><input type="text" name="telefono" class="form-control" value="{{ old('telefono', $supplier->telefono ?? '') }}"></div>
    <div class="col-md-3"><label class="form-label">Celular</label><input type="text" name="celular" class="form-control" value="{{ old('celular', $supplier->celular ?? '') }}"></div>
    <div class="col-md-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email', $supplier->email ?? '') }}"></div>
    <div class="col-md-3"><label class="form-label">Ciudad</label><input type="text" name="ciudad" class="form-control" value="{{ old('ciudad', $supplier->ciudad ?? '') }}"></div>
    <div class="col-md-6"><label class="form-label">Dirección</label><input type="text" name="direccion" class="form-control" value="{{ old('direccion', $supplier->direccion ?? '') }}"></div>
    <div class="col-md-3"><label class="form-label">Contacto principal</label><input type="text" name="contacto_principal" class="form-control" value="{{ old('contacto_principal', $supplier->contacto_principal ?? '') }}"></div>
    <div class="col-md-3">
        <label class="form-label">Estado</label>
        <select name="estado" class="form-select">
            <option value="activo" @selected(old('estado', $supplier->estado ?? 'activo') === 'activo')>Activo</option>
            <option value="inactivo" @selected(old('estado', $supplier->estado ?? '') === 'inactivo')>Inactivo</option>
        </select>
    </div>
    <div class="col-12"><label class="form-label">Observaciones</label><textarea name="observaciones" class="form-control" rows="3">{{ old('observaciones', $supplier->observaciones ?? '') }}</textarea></div>
</div>
