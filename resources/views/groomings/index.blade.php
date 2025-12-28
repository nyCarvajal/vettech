@extends('layouts.app')

@include('groomings.partials.styles')

@section('breadcrumbs')
    <span class="text-gray-400">Inicio</span>
    <span class="mx-2">/</span>
    <span class="font-medium text-gray-700">Peluquería</span>
@endsection

@section('content')
    <div class="rounded-2xl grooming-surface p-5 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Tablero de peluquería</h1>
                <p class="text-sm text-gray-600">Agenda diaria y control de estados.</p>
            </div>
            <a href="{{ route('groomings.create') }}" class="inline-flex items-center gap-2 grooming-btn-primary text-white">
                + Nueva orden
            </a>
        </div>
    </div>

    <x-card class="mt-4 bg-white border-purple-100 shadow-soft grooming-card">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 items-end">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 lg:col-span-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Desde</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="w-full rounded-lg border-purple-200 focus:ring-purple-400 focus:border-purple-400">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Hasta</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="w-full rounded-lg border-purple-200 focus:ring-purple-400 focus:border-purple-400">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Estado</label>
                    <select name="status" class="w-full rounded-lg border-purple-200 focus:ring-purple-400 focus:border-purple-400">
                        <option value="">Todos</option>
                        <option value="agendado" @selected($status==='agendado')>Agendado</option>
                        <option value="en_proceso" @selected($status==='en_proceso')>En proceso</option>
                        <option value="finalizado" @selected($status==='finalizado')>Finalizado</option>
                        <option value="cancelado" @selected($status==='cancelado')>Cancelado</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full inline-flex justify-center grooming-btn-primary">Filtrar</button>
                </div>
            </form>
            <form method="GET" action="{{ route('groomings.create') }}" class="grid grid-cols-1 gap-2 bg-purple-50 border border-purple-100 rounded-xl p-3 shadow-inner">
                <label class="text-sm font-semibold text-purple-800">Ingreso rápido</label>
                <select name="patient_id" class="w-full rounded-lg border-purple-200 focus:ring-purple-400 focus:border-purple-400" required>
                    <option value="">Selecciona paciente</option>
                    @foreach($patients as $pet)
                        <option value="{{ $pet->id }}">{{ $pet->display_name }} ({{ optional($pet->owner)->name }})</option>
                    @endforeach
                </select>
                <button class="inline-flex justify-center items-center grooming-btn-secondary">
                    Enviar a peluquería
                </button>
            </form>
        </div>
    </x-card>

    @php
        $columns = [
            'agendado' => 'Agendado',
            'en_proceso' => 'En proceso',
            'finalizado' => 'Finalizado',
        ];
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
        @foreach($columns as $key => $label)
            <div class="bg-white border border-purple-100 rounded-xl shadow-soft grooming-card">
                <div class="px-4 py-3 border-b border-purple-50 flex items-center justify-between bg-gradient-to-r from-purple-50 to-mint-50 rounded-t-xl border-l-4 border-l-purple-200 grooming-card-accent">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $label }}</p>
                        <p class="text-xs text-gray-500">{{ ($groomings[$key] ?? collect())->count() }} casos</p>
                    </div>
                    <span class="px-3 py-1 text-xs rounded-full bg-purple-100 text-purple-800 font-semibold">{{ strtoupper($key) }}</span>
                </div>
                <div class="p-4 space-y-3">
                    @forelse($groomings[$key] ?? [] as $item)
                        <div class="border border-purple-100 rounded-lg p-4 bg-purple-50/60 border-l-4 border-l-mint-200 grooming-card-accent">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ optional($item->patient)->display_name ?? 'Paciente' }}</p>
                                    <p class="text-xs text-gray-500">Tutor: {{ optional($item->owner)->name }}</p>
                                </div>
                                <div class="text-right text-xs text-gray-500">
                                    <p class="font-medium text-purple-800">{{ optional($item->scheduled_at)->format('H:i') }}</p>
                                    @if($item->service_name)
                                        <p class="text-mint-700">{{ $item->service_name }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2 mt-2 text-xs">
                                @if($item->needs_pickup)
                                    <span class="px-2 py-1 bg-mint-50 text-mint-700 rounded-full">Domicilio</span>
                                @endif
                                @if($item->external_deworming)
                                    <span class="px-2 py-1 bg-amber-50 text-amber-700 rounded-full">Desparasitación</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 mt-3">
                                <a href="{{ route('groomings.show', $item) }}" class="text-sm px-3 py-1.5 rounded-md grooming-btn-secondary">Abrir</a>
                                @if($item->status === 'agendado')
                                    <form method="POST" action="{{ route('groomings.start', $item) }}">
                                        @csrf
                                        <button class="text-sm px-3 py-1.5 rounded-md grooming-btn-primary">Iniciar</button>
                                    </form>
                                @elseif($item->status === 'en_proceso')
                                    <a href="{{ route('groomings.report.create', $item) }}" class="text-sm px-3 py-1.5 rounded-md grooming-btn-primary">Informe</a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No hay casos.</p>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
@endsection
