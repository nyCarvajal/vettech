@php
    $roleOptions = $roles ?? [];
    $selectedRole = old('role', $user->role ?? $defaultRole ?? '');
    $tiposIdentificacion = collect($tipoIdentificaciones ?? []);
    $selectedTipoIdentificacion = old('tipo_identificacion', $user->tipo_identificacion ?? '');
@endphp

{{-- Nombre --}}
<div class="mb-3">
  <label for="nombre" class="form-label">Nombre</label>
  <input id="nombre"
         name="nombre"
         type="text"
         class="form-control @error('nombre') is-invalid @enderror"
         value="{{ old('nombre', $user->nombre ?? '') }}"
         required>
  @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Apellidos --}}
<div class="mb-3">
  <label for="apellidos" class="form-label">Apellidos</label>
  <input id="apellidos"
         name="apellidos"
         type="text"
         class="form-control @error('apellidos') is-invalid @enderror"
         value="{{ old('apellidos', $user->apellidos ?? '') }}"
         required>
  @error('apellidos') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Email --}}
<div class="mb-3">
  <label for="email" class="form-label">Correo electrónico</label>
  <input id="email"
         name="email"
         type="email"
         class="form-control @error('email') is-invalid @enderror"
         value="{{ old('email', $user->email ?? '') }}"
         required>
  @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Rol --}}
<div class="mb-3">
  <label for="role" class="form-label">Rol</label>
  <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
    <option value="">Selecciona un rol</option>
    @foreach($roleOptions as $value => $label)
      <option value="{{ $value }}" {{ strtolower($selectedRole) === strtolower($value) ? 'selected' : '' }}>{{ $label }}</option>
    @endforeach
  </select>
  @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Tipo de identificación --}}
<div class="mb-3">
  <label for="tipo_identificacion" class="form-label">Tipo de identificación</label>
  <select id="tipo_identificacion"
          name="tipo_identificacion"
          class="form-select @error('tipo_identificacion') is-invalid @enderror"
          required>
    <option value="">Selecciona una opción</option>
    @forelse($tiposIdentificacion as $tipo)
      <option value="{{ $tipo->tipo }}" {{ $selectedTipoIdentificacion === $tipo->tipo ? 'selected' : '' }}>{{ $tipo->tipo }}</option>
    @empty
      <option value="" disabled>No hay tipos de identificación configurados</option>
    @endforelse
  </select>
  @error('tipo_identificacion') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Número de identificación --}}
<div class="mb-3">
  <label for="numero_identificacion" class="form-label">Número de identificación</label>
  <input id="numero_identificacion"
         name="numero_identificacion"
         type="text"
         class="form-control @error('numero_identificacion') is-invalid @enderror"
         value="{{ old('numero_identificacion', $user->numero_identificacion ?? '') }}"
         required>
  @error('numero_identificacion') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Dirección --}}
<div class="mb-3">
  <label for="direccion" class="form-label">Dirección</label>
  <input id="direccion"
         name="direccion"
         type="text"
         class="form-control @error('direccion') is-invalid @enderror"
         value="{{ old('direccion', $user->direccion ?? '') }}"
         required>
  @error('direccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- WhatsApp --}}
<div class="mb-3">
  <label for="whatsapp" class="form-label">WhatsApp</label>
  <input id="whatsapp"
         name="whatsapp"
         type="tel"
         class="form-control @error('whatsapp') is-invalid @enderror"
         value="{{ old('whatsapp', $user->whatsapp ?? '') }}"
         placeholder="+57 300 123 4567"
         required>
  @error('whatsapp') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Color --}}
<div class="mb-3">
  <label for="color" class="form-label">Color</label>
  <input id="color"
         name="color"
         type="color"
         class="form-control form-control-color"
         value="{{ old('color', $user->color ?? '#c7b7ff') }}">
</div>

{{-- Firma médica (texto) --}}
<div class="mb-3">
  <label for="firma_medica_texto" class="form-label">Firma médica (texto)</label>
  <input id="firma_medica_texto"
         name="firma_medica_texto"
         type="text"
         class="form-control @error('firma_medica_texto') is-invalid @enderror"
         value="{{ old('firma_medica_texto', $user->firma_medica_texto ?? '') }}"
         placeholder="Nombre del médico o texto de la firma">
  @error('firma_medica_texto') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Imagen de la firma --}}
<div class="mb-3">
  <label for="firma_medica_imagen" class="form-label">Imagen de la firma</label>
  @if(isset($user) && $user->firma_medica_url)
    <div class="mb-2">
      <span class="d-block small text-muted">Firma actual</span>
      <img src="{{ $user->firma_medica_url }}" alt="Firma médica" class="img-thumbnail" style="max-height: 120px;">
    </div>
  @endif
  <input id="firma_medica_imagen"
         name="firma_medica_imagen"
         type="file"
         class="form-control @error('firma_medica_imagen') is-invalid @enderror"
         accept="image/*">
  <div class="form-text">Se cargará de forma segura en Cloudinary.</div>
  @error('firma_medica_imagen') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Password --}}
<div class="mb-3">
  <label for="password" class="form-label">Contraseña</label>
  <input id="password"
         name="password"
         type="password"
         class="form-control @error('password') is-invalid @enderror"
         @if(!isset($user)) required @endif>
  @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- Confirmar Password --}}
<div class="mb-3">
  <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
  <input id="password_confirmation"
         name="password_confirmation"
         type="password"
         class="form-control"
         @if(!isset($user)) required @endif>
</div>
