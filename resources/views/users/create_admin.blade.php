@extends('layouts.vertical', ['subtitle' => 'Crear Usuario'])


@section('content')
<div class="container">
  <h1>Crear Usuario</h1>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
<div class="card card-body" style="background: linear-gradient(135deg, #ede7ff 0%, #d0f5e6 100%); border: none;">
  <form method="POST" action="{{ route('users.admins.store') }}">
    @csrf

    {{-- Los mismos campos que en create_trainer --}}
    @include('users.partials.form-fields', [
        'roles' => $roles ?? [],
        'defaultRole' => $defaultRole ?? 'admin',
        'tipoIdentificaciones' => $tipoIdentificaciones ?? collect(),
    ])

    <button type="submit" class="btn btn-primary">Crear usuario</button>
  </form>
  </div>
</div>
@endsection
