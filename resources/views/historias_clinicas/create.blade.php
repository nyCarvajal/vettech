@extends('layouts.vertical', ['subtitle' => 'Historia clínica'])

@section('content')
<div class="container">
    <h1 class="mb-4">Nueva historia clínica</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('historias-clinicas.store') }}" method="POST" id="historia-clinica-form" data-autosave="{{ route('historias-clinicas.autosave') }}" data-update-url="{{ route('historias-clinicas.update', ['historiaClinica' => '__ID__']) }}">
        @csrf
        @include('historias_clinicas.form')
    </form>
</div>
@endsection
