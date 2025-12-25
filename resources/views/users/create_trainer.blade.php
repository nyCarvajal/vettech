@php
    $trainerLabelSingular = $trainerLabelSingular ?? \App\Models\Peluqueria::defaultRoleLabel(\App\Models\Peluqueria::ROLE_STYLIST);
    $trainerLabelPlural = $trainerLabelPlural ?? \App\Models\Peluqueria::defaultRoleLabel(\App\Models\Peluqueria::ROLE_STYLIST, true);
@endphp

@extends('layouts.app', ['subtitle' => 'Crear ' . $trainerLabelSingular])




@section('content')
  <div class="container">
    <h1>Crear usuario</h1>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
<div class="card card-body" style="background: linear-gradient(135deg, #ede7ff 0%, #d0f5e6 100%); border: none;">
    <form method="POST" action="{{ route('users.trainers.store') }}">
      @csrf

      @include('users.partials.form-fields', [
          'roles' => $roles ?? [],
          'defaultRole' => $defaultRole ?? 'groomer',
          'tipoIdentificaciones' => $tipoIdentificaciones ?? collect(),
      ])

      <button type="submit" class="btn btn-primary">Crear usuario</button>
    </form>
        </div>
  </div>
@endsection

