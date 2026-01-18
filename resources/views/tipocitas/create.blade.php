@extends('layouts.app', ['subtitle' => 'Crear tipo de cita'])

@section('content')
<div class="container">
    <h1 class="mb-4">Crear tipo de cita</h1>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('tipocitas.store') }}" method="POST">
                @include('tipocitas._form')
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">Guardar</button>
                    <a href="{{ route('tipocitas.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
