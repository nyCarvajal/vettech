@extends('layouts.vertical', ['subtitle' => 'Historia clínica'])

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Editar historia clínica</h1>
        <div>
            <small class="text-muted">Última actualización: {{ optional($historia->updated_at)->format('d/m/Y H:i') }}</small>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('historias-clinicas.update', $historia) }}" method="POST" id="historia-clinica-form" data-autosave="{{ route('historias-clinicas.autosave') }}" data-update-url="{{ route('historias-clinicas.update', $historia) }}">
        @csrf
        @method('PUT')
        @include('historias_clinicas.form')
    </form>
</div>
@endsection
