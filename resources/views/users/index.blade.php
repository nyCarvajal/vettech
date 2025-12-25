{{-- resources/views/users/index.blade.php --}}
@php
    $trainerLabelSingular = $trainerLabelSingular ?? \App\Models\Peluqueria::defaultRoleLabel(\App\Models\Peluqueria::ROLE_STYLIST);
    $trainerLabelPlural = $trainerLabelPlural ?? \App\Models\Peluqueria::defaultRoleLabel(\App\Models\Peluqueria::ROLE_STYLIST, true);
@endphp

@extends('layouts.vertical', ['subtitle' => 'Ver Usuarios'])



@section('content')

<div class="container">
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Usuarios</h1>
    <div>
      @php
        $isAdmin = in_array(strtolower(auth()->user()->role ?? ''), ['admin', 'administrator'], true);
      @endphp
      @if($isAdmin)
        <a href="{{ route('users.admins.create') }}" class="btn btn-success">
          + Crear usuario
        </a>
      @endif
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
<div class="card card-body">
  <table class="table table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre completo</th>
        <th>Email</th>
        <th>Rol</th>
        <th>Peluqueria</th>
        <th class="text-end">Acciones</th>
      </tr>
    </thead>
    <tbody>
      @forelse($users as $user)


        <tr>
          <td>{{ $user->id }}</td>
          <td>{{ $user->nombre }} {{ $user->apellidos }}</td>
          <td>{{ $user->email }}</td>
          <td>
		  
            @php
              $normalizedRole = strtolower($user->role ?? '');
              $roleLabelsLower = collect($roleLabels ?? [])->mapWithKeys(fn ($label, $value) => [strtolower($value) => $label]);
            @endphp
            {{ $roleLabelsLower[$normalizedRole] ?? $user->role ?? '—' }}
          </td>
          <td>{{ optional($user->clinica)->nombre ?? '—' }}</td>
          <td class="text-end">
            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary">
              Editar
            </a>
            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline-block"
                  onsubmit="return confirm('¿Eliminar usuario #{{ $user->id }}?');">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-outline-danger">
                Eliminar
              </button>
            </form>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="6" class="text-center">No hay usuarios registrados.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>
  @if(method_exists($users, 'links'))
    <div class="d-flex justify-content-center">
      {{ $users->links() }}
    </div>
  @endif
</div>
@endsection
