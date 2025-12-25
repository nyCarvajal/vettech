@extends('layouts.app', ['subtitle' => 'Editar Usuario'])

@section('content')
  <div class="container">
    <h1>Editar Usuario</h1>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
<div class="card card-body" style="background: linear-gradient(135deg, #ede7ff 0%, #d0f5e6 100%); border: none;">
    <form method="POST" action="{{ route('users.update', $user) }}">
      @csrf
      @method('PUT')

      @include('users.partials.form-fields', [
          'user' => $user,
          'roles' => $roles ?? [],
          'defaultRole' => $user->role ?? null,
          'tipoIdentificaciones' => $tipoIdentificaciones ?? collect(),
      ])

      <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    </form>
    </div>
  </div>
@endsection

