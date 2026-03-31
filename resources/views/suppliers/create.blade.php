@extends('layouts.app', ['subtitle' => 'Nuevo proveedor'])
@section('content')<div class="container-fluid"><div class="card"><div class="card-body"><form method="POST" action="{{ route('suppliers.store') }}">@include('suppliers._form')<div class="mt-3"><button class="btn btn-primary">Guardar</button></div></form></div></div></div>@endsection
