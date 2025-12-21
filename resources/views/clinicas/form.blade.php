<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nombre</label>
        <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $clinica->nombre) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Slug</label>
        <input type="text" name="slug" class="form-control" value="{{ old('slug', $clinica->slug) }}" placeholder="identificador-unico">
    </div>
    <div class="col-md-6">
        <label class="form-label">Correo</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $clinica->email) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Teléfono</label>
        <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $clinica->telefono) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Color primario</label>
        <input type="text" name="color" class="form-control" value="{{ old('color', $clinica->color) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Color del menú</label>
        <input type="text" name="menu_color" class="form-control" value="{{ old('menu_color', $clinica->menu_color) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Color de la topbar</label>
        <input type="text" name="topbar_color" class="form-control" value="{{ old('topbar_color', $clinica->topbar_color) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Etiqueta singular del profesional</label>
        <input type="text" name="trainer_label_singular" class="form-control" value="{{ old('trainer_label_singular', $clinica->trainer_label_singular) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Etiqueta plural del profesional</label>
        <input type="text" name="trainer_label_plural" class="form-control" value="{{ old('trainer_label_plural', $clinica->trainer_label_plural) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">NIT</label>
        <input type="text" name="nit" class="form-control" value="{{ old('nit', $clinica->nit) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Dirección</label>
        <input type="text" name="direccion" class="form-control" value="{{ old('direccion', $clinica->direccion) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Municipio</label>
        <input type="number" name="municipio" class="form-control" value="{{ old('municipio', $clinica->municipio) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Mensaje de bienvenida</label>
        <textarea name="msj_bienvenida" class="form-control" rows="2">{{ old('msj_bienvenida', $clinica->msj_bienvenida) }}</textarea>
    </div>
    <div class="col-md-4">
        <label class="form-label">Mensaje de confirmación</label>
        <textarea name="msj_reserva_confirmada" class="form-control" rows="2">{{ old('msj_reserva_confirmada', $clinica->msj_reserva_confirmada) }}</textarea>
    </div>
    <div class="col-md-12">
        <label class="form-label">Mensaje de cierre</label>
        <textarea name="msj_finalizado" class="form-control" rows="2">{{ old('msj_finalizado', $clinica->msj_finalizado) }}</textarea>
    </div>
</div>
