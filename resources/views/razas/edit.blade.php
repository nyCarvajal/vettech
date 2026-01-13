@extends('layouts.app', ['subtitle' => 'Editar raza'])

@section('content')
<div class="container">
    <h1 class="mb-4">Editar raza</h1>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('razas.update', $raza) }}" method="POST">
                @method('PUT')
                @include('razas._form')
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">Guardar</button>
                    <a href="{{ route('razas.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
