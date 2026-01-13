@extends('layouts.app', ['subtitle' => 'Crear especie'])

@section('content')
<div class="container">
    <h1 class="mb-4">Crear especie</h1>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('especies.store') }}" method="POST">
                @include('especies._form')
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">Guardar</button>
                    <a href="{{ route('especies.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
