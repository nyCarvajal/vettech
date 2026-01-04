@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6 space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold">{{ $consentTemplate->name }}</h1>
            <p class="text-gray-600">{{ $consentTemplate->category }}</p>
        </div>
        <a href="{{ route('consent-templates.edit', $consentTemplate) }}" class="text-indigo-600">Editar</a>
    </div>
    <div class="bg-white shadow rounded p-4">
        <div class="prose max-w-none">{!! $consentTemplate->body_html !!}</div>
    </div>
</div>
@endsection
