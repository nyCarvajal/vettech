@extends('layouts.app', ['subtitle' => 'Alertas de facturas'])
@section('content')
<div class="container-fluid">
<h3>Alertas de vencimiento</h3>
<div class="alert alert-danger">Vencidas: {{ $overdue->count() }}</div>
<div class="alert alert-warning">Vencen hoy: {{ $dueToday->count() }}</div>
<div class="alert alert-info">Próximas ({{ $days }} días): {{ $upcoming->count() }}</div>
</div>
@endsection
