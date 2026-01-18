@extends('layouts.app', ['subtitle' => 'Configuración'])

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h3 mb-2">Configuración de catálogos</h1>
            <p class="text-muted mb-0">Gestiona los listados base para ítems, citas y pacientes.</p>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h6">Áreas de ítems</h2>
                    <p class="text-muted">Crea, edita o elimina las áreas que clasifican los ítems.</p>
                    <a href="{{ route('areas.index') }}" class="btn btn-outline-primary">Gestionar áreas</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h6">Bancos</h2>
                    <p class="text-muted">Administra bancos para pagos y conciliaciones.</p>
                    <a href="{{ route('bancos.index') }}" class="btn btn-outline-primary">Gestionar bancos</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h6">Tipos de identificación</h2>
                    <p class="text-muted">Define los documentos disponibles para tutores y clientes.</p>
                    <a href="{{ route('tipo-identificaciones.index') }}" class="btn btn-outline-primary">Gestionar identificaciones</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h6">Tipos de cita</h2>
                    <p class="text-muted">Configura los tipos de cita disponibles en la agenda.</p>
                    <a href="{{ route('tipocitas.index') }}" class="btn btn-outline-primary">Gestionar tipos de cita</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h6">Especies</h2>
                    <p class="text-muted">Agrega o actualiza las especies registradas.</p>
                    <a href="{{ route('especies.index') }}" class="btn btn-outline-primary">Gestionar especies</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h6">Razas</h2>
                    <p class="text-muted">Organiza las razas por especie.</p>
                    <a href="{{ route('razas.index') }}" class="btn btn-outline-primary">Gestionar razas</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
