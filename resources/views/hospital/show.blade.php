@extends('layouts.app')

@section('content')
@php
    $role = strtolower((string) (auth()->user()->role ?? ''));
    $isAdmin = in_array($role, ['admin', 'administrator'], true);
    $isMedico = $role === 'medico';
    $currentDay = $stay->days->sortByDesc('date')->first();
    $activeOrders = $stay->orders->where('status', 'active');
@endphp

<div class="max-w-7xl mx-auto space-y-4">
    <x-card class="bg-white border border-emerald-100">
        @php($owner = $stay->owner ?? $stay->patient?->owner)
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <p class="text-sm text-gray-500">Día {{ $stay->daysSinceAdmission() }} · {{ ucfirst($stay->severity) }}</p>
                <h2 class="text-2xl font-semibold text-emerald-700">{{ $stay->patient->display_name ?? 'Paciente' }}</h2>
                <p class="text-sm text-gray-600">Tutor: {{ $owner->name ?? 'N/D' }}</p>
            </div>
            <div class="flex gap-2">
                <form method="POST" action="{{ route('hospital.discharge', $stay) }}">@csrf <x-button type="submit" color="danger">{{ $isAdmin ? 'Dar alta' : 'Solicitar alta' }}</x-button></form>
                <a href="{{ route('hospital.index') }}" class="inline-flex"><x-button color="secondary">Ver resumen</x-button></a>
                @if($isAdmin)
                    <form method="POST" action="{{ route('hospital.invoice', $stay) }}">@csrf <x-button>Generar factura</x-button></form>
                @endif
            </div>
        </div>
    </x-card>

    <div class="bg-emerald-50 border border-emerald-100 rounded-lg p-3">
        <div class="flex gap-2 text-sm mb-3">
            <a href="#ordenes" class="px-3 py-1 rounded-full bg-white shadow">Órdenes</a>
            <a href="#aplicaciones" class="px-3 py-1 rounded-full bg-white shadow">Aplicaciones</a>
            <a href="#evolucion" class="px-3 py-1 rounded-full bg-white shadow">Evolución</a>
            <a href="#signos" class="px-3 py-1 rounded-full bg-white shadow">Signos</a>
            @if($isAdmin)<a href="#facturacion" class="px-3 py-1 rounded-full bg-white shadow">Facturación</a>@endif
        </div>

        <details class="mb-3" open>
            <summary class="cursor-pointer font-semibold text-emerald-700">Timeline de días</summary>
            <div class="mt-2 max-h-36 overflow-y-auto space-y-1">
                @foreach($stay->days->sortByDesc('date') as $day)
                    <div class="text-sm border rounded px-2 py-1 bg-white">Día {{ $day->day_number }} · {{ $day->date->format('d/m/Y') }}</div>
                @endforeach
            </div>
        </details>

        <section id="ordenes" class="space-y-2 mb-4">
            <h3 class="font-semibold text-emerald-700">ÓRDENES ACTIVAS</h3>
            @foreach($activeOrders as $order)
                <x-card class="border border-emerald-100">
                    <div class="flex justify-between items-center gap-3 flex-wrap">
                        <div>
                            <p class="font-semibold">{{ $order->manual_name ?? $order->product?->name ?? 'Tratamiento' }}</p>
                            <p class="text-sm text-gray-600">{{ $order->dose }} · {{ $order->route }} · {{ $order->frequency_type }} ({{ $order->frequency_value }})</p>
                            <p class="text-sm">Última: {{ optional($order->last_applied_at)?->format('d/m H:i') ?? 'Sin aplicaciones' }}</p>
                            <p class="text-sm">Próxima: {{ optional($order->next_due_at)?->format('d/m H:i') ?? 'Finalizada' }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($order->next_due_at && $order->next_due_at->lte(now()->addMinutes(30)))
                                <x-badge color="warning">Pendiente</x-badge>
                            @endif
                            <form method="POST" action="{{ route('hospital.orders.apply', ['stay' => $stay->id, 'order' => $order->id]) }}" class="flex gap-2">
                                @csrf
                                <input name="notes" class="border rounded px-2 py-1 text-sm" placeholder="Obs. opcional" />
                                <x-button type="submit">APLICAR</x-button>
                            </form>
                            @if($isAdmin)
                                <form method="POST" action="{{ route('hospital.orders.stop', ['order' => $order->id]) }}">@csrf<x-button type="submit" size="sm" color="danger">Detener</x-button></form>
                            @endif
                        </div>
                    </div>
                </x-card>
            @endforeach

            <x-card class="bg-white border border-emerald-100">
                <h4 class="font-semibold">Nueva orden</h4>
                <form method="POST" action="{{ route('hospital.orders.store', $stay) }}" class="grid md:grid-cols-4 gap-2 mt-2">@csrf
                    <x-select name="source"><option value="inventory">Inventario</option><option value="manual">Manual</option></x-select>
                    <x-input name="product_id" placeholder="Producto ID" />
                    <x-input name="manual_name" placeholder="Nombre manual" />
                    <x-input name="dose" placeholder="Dosis" />
                    <x-input name="route" placeholder="Vía" />
                    <x-select name="frequency_type"><option value="q_hours">Cada X horas</option><option value="times_per_day">X veces al día</option><option value="q_days">Cada X días</option></x-select>
                    <x-input name="frequency_value" type="number" min="1" value="8" placeholder="Valor" />
                    <x-input name="start_at" type="datetime-local" value="{{ now()->format('Y-m-d\TH:i') }}" />
                    <x-input name="duration_days" type="number" min="1" placeholder="Duración días" />
                    <input type="hidden" name="type" value="medication" /><input type="hidden" name="created_by" value="{{ auth()->id() }}" />
                    <div class="md:col-span-4"><x-button type="submit" size="sm">Guardar orden</x-button></div>
                </form>
            </x-card>
        </section>

        <section id="aplicaciones" class="mb-4">
            <h3 class="font-semibold text-emerald-700">APLICACIONES DEL DÍA</h3>
            @foreach(($currentDay?->administrations ?? collect())->sortByDesc('administered_at') as $admin)
                <div class="border rounded p-2 bg-white text-sm">{{ $admin->administered_at?->format('H:i') }} · {{ $admin->order->manual_name ?? $admin->order->product?->name }} · {{ $admin->dose_given }}</div>
            @endforeach
        </section>

        <section id="evolucion" class="mb-4">
            <h3 class="font-semibold text-emerald-700">EVOLUCIÓN</h3>
            <form method="POST" action="{{ route('hospital.progress.store', $stay) }}" class="grid md:grid-cols-4 gap-2">@csrf
                <x-select name="shift"><option value="manana">Mañana</option><option value="tarde">Tarde</option><option value="noche">Noche</option></x-select>
                <x-textarea name="content" class="md:col-span-2" />
                <input type="hidden" name="logged_at" value="{{ now()->format('Y-m-d\TH:i') }}" /><input type="hidden" name="author_id" value="{{ auth()->id() }}" />
                <x-button type="submit">Guardar</x-button>
            </form>
        </section>

        <section id="signos" class="mb-2">
            <h3 class="font-semibold text-emerald-700">SIGNOS VITALES</h3>
            <form method="POST" action="{{ route('hospital.vitals.store', $stay) }}" class="grid md:grid-cols-5 gap-2">@csrf
                <x-input name="measured_at" type="datetime-local" value="{{ now()->format('Y-m-d\TH:i') }}" />
                <x-input name="temp" placeholder="Temp" /><x-input name="hr" placeholder="FC" /><x-input name="rr" placeholder="FR" /><x-input name="spo2" placeholder="SpO2" />
                <input type="hidden" name="measured_by" value="{{ auth()->id() }}" />
                <div class="md:col-span-5"><x-button type="submit">Guardar signos</x-button></div>
            </form>
        </section>

        @if($isAdmin)
        <section id="facturacion" class="mt-6">
            <h3 class="font-semibold text-emerald-700">Facturación</h3>
            <p class="text-sm text-gray-500 mb-2">Subtotal: ${{ number_format($stay->charges->sum('total'),2) }}</p>
            @foreach($stay->charges as $charge)
                <div class="flex justify-between border rounded bg-white p-2 text-sm mb-1">
                    <span>{{ $charge->description }} ({{ $charge->status }})</span>
                    <span>${{ number_format($charge->total,2) }}</span>
                </div>
            @endforeach
        </section>
        @endif
    </div>
</div>
@endsection
