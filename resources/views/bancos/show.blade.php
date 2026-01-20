@extends('layouts.app', ['subtitle' => 'Inicio'])

@section('content')
<div class="container">
    <h1 class="mb-4">Banco #{{ $banco->id }}</h1>

    <div class="card">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-4">Nombre</dt>
                <dd class="col-sm-8">{{ $banco->nombre }}</dd>

                <dt class="col-sm-4">Saldo Inicial</dt>
                <dd class="col-sm-8">{{ number_format($banco->saldo_inicial, 2, ',', '.') }}</dd>

                <dt class="col-sm-4">Saldo Actual</dt>
                <dd class="col-sm-8">{{ number_format($banco->saldo_actual, 2, ',', '.') }}</dd>
            </dl>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('bancos.edit', $banco) }}" class="btn btn-warning">Editar</a>
        <a href="{{ route('bancos.index') }}" class="btn btn-secondary">Volver</a>
    </div>
</div>
@endsection
