@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Editar clínica</h4>
        <a href="{{ ($isOwnClinic ?? false) ? route('clinicas.perfil') : route('clinicas.index') }}" class="btn btn-outline-secondary">Volver</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ ($isOwnClinic ?? false) ? route('clinicas.update-own') : route('clinicas.update', $clinica) }}">
                @csrf
                @method('PUT')
                @include('clinicas.form')

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>

            @unless ($isOwnClinic ?? false)
                <div class="mt-3 text-start">
                    <form method="POST" action="{{ route('clinicas.destroy', $clinica) }}" onsubmit="return confirm('¿Eliminar esta clínica?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">Eliminar</button>
                    </form>
                </div>
            @endunless
        </div>
    </div>
</div>
@endsection
