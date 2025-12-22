@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">{{ $owner->exists ? 'Editar tutor' : 'Nuevo tutor' }}</h1>
            <p class="text-muted mb-0">Completa los datos de contacto del tutor.</p>
        </div>
        <a href="{{ route('owners.index') }}" class="btn btn-outline-secondary">Volver</a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form method="post" action="{{ $owner->exists ? route('owners.update', $owner) : route('owners.store') }}">
                @csrf
                @if($owner->exists)
                    @method('PUT')
                @endif
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nombre completo</label>
                        <input type="text" name="name" value="{{ old('name', $owner->name) }}" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="phone" value="{{ old('phone', $owner->phone) }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">WhatsApp</label>
                        <input type="text" name="whatsapp" value="{{ old('whatsapp', $owner->whatsapp) }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email', $owner->email) }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Documento</label>
                        <input type="text" name="document" value="{{ old('document', $owner->document) }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="address" value="{{ old('address', $owner->address) }}" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Notas</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $owner->notes) }}</textarea>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-4">
                    <button class="btn btn-primary" type="submit">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
