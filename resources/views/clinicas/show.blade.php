@extends('layouts.vertical')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Perfil de clínica</h4>
        <a href="{{ route('clinicas.edit', $clinica) }}" class="btn btn-outline-primary">Editar</a>
    </div>

    <div class="card">
        <div class="card-body row g-3">
            <div class="col-md-6">
                <h6 class="text-muted">Nombre</h6>
                <p class="mb-0">{{ $clinica->nombre }}</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted">Correo</h6>
                <p class="mb-0">{{ $clinica->email ?? '—' }}</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted">Teléfono</h6>
                <p class="mb-0">{{ $clinica->telefono ?? '—' }}</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted">Dirección</h6>
                <p class="mb-0">{{ $clinica->direccion ?? '—' }}</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted">Color</h6>
                <p class="mb-0">{{ $clinica->color ?? '—' }}</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted">Slug</h6>
                <p class="mb-0">{{ $clinica->slug ?? '—' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
