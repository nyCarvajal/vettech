@extends('layouts.app')

@section('content')
@php
    $role = strtolower((string) (auth()->user()->role ?? ''));
    $isAdmin = in_array($role, ['admin', 'administrator'], true);
    $owner = $stay->owner ?? $stay->patient?->owner;
    $currentDay = $stay->days->sortByDesc('date')->first();
    $activeOrders = $stay->orders->where('status', 'active')->sortBy('next_due_at');
    $pendingOrders = $activeOrders->filter(fn ($order) => $order->next_due_at && $order->next_due_at->lte(now()->addMinutes(30)));
    $todayProgress = ($currentDay?->progressNotes ?? collect())->sortByDesc('logged_at');
    $todayVitals = ($currentDay?->vitals ?? collect())->sortByDesc('measured_at');
    $recentOrders = $stay->orders->sortByDesc('created_at')->take(6);
@endphp

<div class="mx-auto max-w-7xl space-y-5 pb-10">
    <section class="rounded-3xl border border-emerald-100 bg-gradient-to-r from-emerald-50 via-teal-50 to-cyan-50 p-5 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="space-y-1">
                <p class="text-xs font-semibold uppercase tracking-widest text-emerald-700/80">Hospitalización activa</p>
                <h1 class="text-3xl font-extrabold tracking-tight text-slate-800">{{ $stay->patient->display_name ?? 'Paciente' }}</h1>
                <p class="text-sm text-slate-600">Tutor: <span class="font-semibold">{{ $owner->name ?? 'N/D' }}</span> · {{ $owner->phone ?? 'Sin teléfono' }}</p>
                <div class="flex flex-wrap items-center gap-2 pt-1">
                    <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-700 shadow-sm">Día {{ $stay->daysSinceAdmission() }}</span>
                    <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-emerald-700 shadow-sm">{{ ucfirst($stay->severity) }}</span>
                    <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-amber-700 shadow-sm">{{ $pendingOrders->count() }} pendientes</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 text-center sm:grid-cols-3">
                <div class="rounded-2xl bg-white/90 p-3 shadow-sm">
                    <p class="text-xs text-slate-500">Órdenes activas</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $activeOrders->count() }}</p>
                </div>
                <div class="rounded-2xl bg-white/90 p-3 shadow-sm">
                    <p class="text-xs text-slate-500">Aplicaciones hoy</p>
                    <p class="text-2xl font-bold text-slate-800">{{ ($currentDay?->administrations ?? collect())->count() }}</p>
                </div>
                <div class="rounded-2xl bg-white/90 p-3 shadow-sm col-span-2 sm:col-span-1">
                    <p class="text-xs text-slate-500">Evoluciones</p>
                    <p class="text-2xl font-bold text-slate-800">{{ ($currentDay?->progressNotes ?? collect())->count() }}</p>
                </div>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap gap-2">
            <form method="POST" action="{{ route('hospital.discharge', $stay) }}">@csrf
                <x-button type="submit" color="danger">{{ $isAdmin ? 'Dar alta' : 'Solicitar alta' }}</x-button>
            </form>
            <a href="{{ route('hospital.index') }}" class="inline-flex"><x-button color="secondary">Ver resumen</x-button></a>
            @if($isAdmin)
                <form method="POST" action="{{ route('hospital.invoice', $stay) }}">@csrf <x-button>Generar factura</x-button></form>
            @endif
        </div>
    </section>

    <section class="sticky top-2 z-10 rounded-2xl border border-slate-200 bg-white/95 p-2 shadow-sm backdrop-blur">
        <nav class="grid grid-cols-2 gap-2 text-sm font-semibold md:grid-cols-5">
            <a href="#ordenes" class="rounded-xl bg-slate-900 px-3 py-2 text-center text-white">Órdenes</a>
            <a href="#aplicaciones" class="rounded-xl bg-slate-100 px-3 py-2 text-center text-slate-700 hover:bg-slate-200">Aplicaciones</a>
            <a href="#evolucion" class="rounded-xl bg-slate-100 px-3 py-2 text-center text-slate-700 hover:bg-slate-200">Evolución</a>
            <a href="#signos" class="rounded-xl bg-slate-100 px-3 py-2 text-center text-slate-700 hover:bg-slate-200">Signos</a>
            @if($isAdmin)
                <a href="#facturacion" class="rounded-xl bg-slate-100 px-3 py-2 text-center text-slate-700 hover:bg-slate-200">Facturación</a>
            @endif
        </nav>
    </section>

    <div class="grid gap-5 lg:grid-cols-3">
        <div class="space-y-5 lg:col-span-2">
            <section id="ordenes" class="space-y-3 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-slate-800">Órdenes activas</h2>
                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">Pendientes: {{ $pendingOrders->count() }}</span>
                </div>

                <div class="space-y-3">
                    @forelse($activeOrders as $order)
                        @php
                            $isPending = $order->next_due_at && $order->next_due_at->lte(now()->addMinutes(30));
                            $orderName = $order->manual_name ?? $order->product?->name ?? 'Tratamiento';
                        @endphp
                        <article class="rounded-2xl border {{ $isPending ? 'border-amber-200 bg-amber-50/60' : 'border-slate-200 bg-slate-50/70' }} p-4">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <h3 class="text-lg font-bold text-slate-800">{{ $orderName }}</h3>
                                    <p class="text-sm text-slate-600">{{ $order->dose ?: 'Sin dosis' }} · {{ $order->route ?: 'Sin vía' }} · {{ strtoupper((string) $order->frequency_type) }} ({{ $order->frequency_value }})</p>
                                    <div class="mt-2 grid gap-1 text-sm text-slate-600 sm:grid-cols-2">
                                        <p>Última: <span class="font-semibold text-slate-800">{{ optional($order->last_applied_at)?->format('d/m H:i') ?? 'Sin aplicaciones' }}</span></p>
                                        <p>Próxima: <span class="font-semibold {{ $isPending ? 'text-amber-700' : 'text-emerald-700' }}">{{ optional($order->next_due_at)?->format('d/m H:i') ?? 'Finalizada' }}</span></p>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end gap-2">
                                    @if($isPending)
                                        <span class="rounded-full bg-amber-200 px-3 py-1 text-xs font-bold text-amber-800">PENDIENTE</span>
                                    @endif
                                    <form method="POST" action="{{ route('hospital.orders.apply', ['stay' => $stay->id, 'order' => $order->id]) }}" class="flex flex-wrap items-center gap-2">
                                        @csrf
                                        <input name="notes" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-200" placeholder="Observación opcional" />
                                        <x-button type="submit" class="!rounded-xl !px-5 !py-2.5 !text-sm !font-bold">APLICAR</x-button>
                                    </form>
                                    @if($isAdmin)
                                        <form method="POST" action="{{ route('hospital.orders.stop', ['order' => $order->id]) }}">@csrf
                                            <x-button type="submit" size="sm" color="danger">Detener</x-button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 p-6 text-center text-slate-500">No hay órdenes activas.</div>
                    @endforelse
                </div>
            </section>

            <section class="space-y-3 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-xl font-bold text-slate-800">Medicamentos / órdenes recientes</h2>
                <div class="space-y-2">
                    @forelse($recentOrders as $order)
                        <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
                            <div>
                                <p class="font-semibold text-slate-800">{{ $order->manual_name ?? $order->product?->name ?? 'Tratamiento' }}</p>
                                <p class="text-slate-600">{{ $order->dose ?: 'Sin dosis' }} · {{ $order->route ?: 'Sin vía' }}</p>
                            </div>
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $order->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-700' }}">{{ $order->status }}</span>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 p-6 text-center text-slate-500">Aún no hay órdenes registradas.</div>
                    @endforelse
                </div>
            </section>

            <section id="aplicaciones" class="space-y-3 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-xl font-bold text-slate-800">Aplicaciones del día</h2>
                <div class="space-y-2">
                    @forelse(($currentDay?->administrations ?? collect())->sortByDesc('administered_at') as $admin)
                        <div class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3">
                            <span class="mt-1 h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-slate-800">{{ $admin->order->manual_name ?? $admin->order->product?->name }}</p>
                                <p class="text-sm text-slate-600">{{ $admin->dose_given ?: 'Sin dosis registrada' }}</p>
                            </div>
                            <p class="text-sm font-semibold text-slate-700">{{ $admin->administered_at?->format('H:i') }}</p>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 p-6 text-center text-slate-500">Sin aplicaciones registradas hoy.</div>
                    @endforelse
                </div>
            </section>
        </div>

        <aside class="space-y-5">
            <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <details open>
                    <summary class="cursor-pointer text-lg font-bold text-slate-800">Timeline de días</summary>
                    <div class="mt-3 max-h-56 space-y-2 overflow-y-auto pr-1">
                        @foreach($stay->days->sortByDesc('date') as $day)
                            <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700">
                                <p class="font-semibold">Día {{ $day->day_number }}</p>
                                <p>{{ $day->date->format('d/m/Y') }}</p>
                            </div>
                        @endforeach
                    </div>
                </details>
            </section>

            <section id="evolucion" class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-bold text-slate-800">Evolución rápida</h2>
                <form method="POST" action="{{ route('hospital.progress.store', $stay) }}" class="mt-3 space-y-3">@csrf
                    <x-select name="shift">
                        <option value="manana">Mañana</option><option value="tarde">Tarde</option><option value="noche">Noche</option>
                    </x-select>
                    <x-textarea name="content" rows="3" placeholder="Resumen breve del turno..." />
                    <input type="hidden" name="logged_at" value="{{ now()->format('Y-m-d\TH:i') }}" />
                    <input type="hidden" name="author_id" value="{{ auth()->id() }}" />
                    <x-button type="submit" class="w-full">Guardar evolución</x-button>
                </form>

                <div class="mt-3 space-y-2">
                    @forelse($todayProgress as $note)
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-2">
                            <p class="text-xs text-slate-500">{{ $note->logged_at?->format('d/m H:i') }} · {{ ucfirst($note->shift ?? 'turno') }}</p>
                            <p class="text-sm text-slate-700">{{ $note->content }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Sin evolución registrada hoy.</p>
                    @endforelse
                </div>
            </section>

            <section id="signos" class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-bold text-slate-800">Signos vitales</h2>
                <form method="POST" action="{{ route('hospital.vitals.store', $stay) }}" class="mt-3 grid grid-cols-2 gap-2">@csrf
                    <x-input name="measured_at" type="datetime-local" value="{{ now()->format('Y-m-d\TH:i') }}" class="col-span-2" />
                    <x-input name="temp" placeholder="Temp" />
                    <x-input name="hr" placeholder="FC" />
                    <x-input name="rr" placeholder="FR" />
                    <x-input name="spo2" placeholder="SpO2" />
                    <input type="hidden" name="measured_by" value="{{ auth()->id() }}" />
                    <x-button type="submit" class="col-span-2">Guardar signos</x-button>
                </form>

                <div class="mt-3 space-y-2">
                    @forelse($todayVitals as $vital)
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-2 py-1.5 text-sm text-slate-700">
                            <p class="text-xs text-slate-500">{{ $vital->measured_at?->format('d/m H:i') }}</p>
                            <p>T: {{ $vital->temp ?? '--' }} · FC: {{ $vital->hr ?? '--' }} · FR: {{ $vital->rr ?? '--' }} · SpO2: {{ $vital->spo2 ?? '--' }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Sin signos registrados hoy.</p>
                    @endforelse
                </div>
            </section>
        </aside>
    </div>

    @if($isAdmin)
        <section id="facturacion" class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-xl font-bold text-slate-800">Facturación</h2>
                <span class="rounded-full bg-emerald-100 px-3 py-1 text-sm font-semibold text-emerald-700">Subtotal: ${{ number_format($stay->charges->sum('total'),2) }}</span>
            </div>
            <div class="space-y-2">
                @foreach($stay->charges as $charge)
                    <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
                        <p class="text-slate-700">{{ $charge->description }} <span class="text-slate-500">({{ $charge->status }})</span></p>
                        <p class="font-semibold text-slate-800">${{ number_format($charge->total,2) }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-xl font-bold text-slate-800">Nueva orden</h2>
        <form method="POST" action="{{ route('hospital.orders.store', $stay) }}" class="mt-3 grid gap-2 md:grid-cols-4">@csrf
            <x-select name="source"><option value="inventory">Inventario</option><option value="manual">Manual</option></x-select>
            <x-input name="product_id" placeholder="Producto ID" />
            <x-input name="manual_name" placeholder="Nombre manual" />
            <x-input name="dose" placeholder="Dosis" />
            <x-input name="route" placeholder="Vía" />
            <x-select name="frequency_type"><option value="q_hours">Cada X horas</option><option value="times_per_day">X veces al día</option><option value="q_days">Cada X días</option></x-select>
            <x-input name="frequency_value" type="number" min="1" value="8" placeholder="Valor" />
            <x-input name="start_at" type="datetime-local" value="{{ now()->format('Y-m-d\TH:i') }}" />
            <x-input name="duration_days" type="number" min="1" placeholder="Duración días" />
            <input type="hidden" name="type" value="medication" />
            <input type="hidden" name="created_by" value="{{ auth()->id() }}" />
            <div class="md:col-span-4"><x-button type="submit">Guardar orden</x-button></div>
        </form>
    </section>
</div>
@endsection
