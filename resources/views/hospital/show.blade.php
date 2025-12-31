@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-4">
    <x-card class="bg-white border border-emerald-100">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3">
                <img src="{{ $stay->patient->photo_url ?? 'https://placekitten.com/120/120' }}" class="w-16 h-16 rounded-full object-cover" />
                <div>
                    <p class="text-sm text-gray-500">Hospitalizado desde {{ $stay->admitted_at->format('d M Y H:i') }}</p>
                    <h2 class="text-xl font-semibold text-emerald-700">{{ $stay->patient->name ?? 'Paciente' }}</h2>
                    <div class="flex gap-2 items-center text-sm text-gray-600">
                        <x-badge color="success">{{ ucfirst($stay->severity) }}</x-badge>
                        <x-badge>Hospitalizado</x-badge>
                    </div>
                </div>
            </div>
            <div class="flex-1 grid md:grid-cols-3 gap-4">
                <div>
                    <p class="text-xs text-gray-500">Peso</p>
                    <p class="font-semibold text-emerald-700">{{ $stay->patient->weight ?? '--' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Alergias</p>
                    <p class="font-semibold text-emerald-700">{{ $stay->patient->allergies ?? 'N/D' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Tutor</p>
                    <p class="font-semibold text-emerald-700">{{ $stay->owner->name ?? '' }}</p>
                    <p class="text-sm text-gray-500">{{ $stay->owner->phone ?? '' }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <form method="POST" action="{{ route('hospital.discharge', $stay) }}">
                    @csrf
                    <x-button type="submit" color="danger">Dar alta</x-button>
                </form>
                <form method="POST" action="{{ route('hospital.invoice', $stay) }}">
                    @csrf
                    <x-button>Generar factura</x-button>
                </form>
            </div>
        </div>
    </x-card>

    <div class="grid lg:grid-cols-8 gap-4">
        <div class="lg:col-span-6 space-y-3">
            <div class="bg-emerald-50 border border-emerald-100 rounded-lg">
                <div class="flex flex-wrap gap-2 border-b border-emerald-100 px-4 py-2">
                    @foreach($stay->days as $day)
                        <a href="#day-{{ $day->id }}" class="px-3 py-1 rounded-full text-sm {{ $loop->first ? 'bg-white shadow' : 'bg-emerald-100' }}">Day {{ $day->day_number }} • {{ $day->date->format('d/m') }}</a>
                    @endforeach
                </div>
                @foreach($stay->days as $day)
                    <div id="day-{{ $day->id }}" class="p-4 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-emerald-700">Día {{ $day->day_number }} ({{ $day->date->format('d M') }})</h3>
                            <p class="text-sm text-gray-500">{{ $day->notes }}</p>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <x-card class="bg-white border border-emerald-100">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-semibold text-emerald-700">Tratamiento / Órdenes</h4>
                                    @php($firstOrder = $day->orders->first())
                                    @if($firstOrder)
                                        <form method="POST" action="{{ route('hospital.orders.stop', ['order' => $firstOrder->id]) }}" class="hidden"></form>
                                    @endif
                                </div>
                                <div class="space-y-2">
                                    @foreach($day->orders as $order)
                                        <div class="p-3 rounded border border-emerald-100">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <p class="font-semibold text-emerald-700">{{ $order->source === 'inventory' ? ($order->product->name ?? 'Producto') : $order->manual_name }}</p>
                                                    <p class="text-sm text-gray-600">{{ $order->dose }} • {{ $order->route }} • {{ $order->frequency }}</p>
                                                </div>
                                                <form method="POST" action="{{ route('hospital.orders.stop', ['order' => $order->id]) }}">
                                                    @csrf
                                                    <x-button type="submit" size="sm" color="danger">Detener</x-button>
                                                </form>
                                            </div>
                                            <form method="POST" action="{{ route('hospital.orders.administrations', ['order' => $order->id]) }}" class="mt-2 flex items-center gap-2">
                                                @csrf
                                                <x-input name="administered_at" type="datetime-local" class="w-48" />
                                                <x-input name="dose_given" placeholder="Dosis" class="w-32" />
                                                <input type="hidden" name="status" value="done" />
                                                <x-input type="hidden" name="administered_by" value="{{ auth()->id() }}" />
                                                <x-button type="submit" size="sm">Aplicar</x-button>
                                            </form>
                                        </div>
                                    @endforeach
                                    <form method="POST" action="{{ route('hospital.orders.store', $stay) }}" class="space-y-2 p-3 rounded border border-dashed border-emerald-200">
                                        @csrf
                                        <div class="grid grid-cols-2 gap-2">
                                            <x-select name="source" label="Tipo">
                                                <option value="inventory">Inventario</option>
                                                <option value="manual">Manual</option>
                                            </x-select>
                                            <x-select name="type" label="Categoría">
                                                <option value="medication">Medicamento</option>
                                                <option value="procedure">Procedimiento</option>
                                                <option value="feeding">Alimentación</option>
                                                <option value="fluid">Fluidos</option>
                                                <option value="other">Otro</option>
                                            </x-select>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <x-input name="product_id" label="Producto (id)" />
                                            <x-input name="manual_name" label="Nombre manual" />
                                        </div>
                                        <div class="grid grid-cols-3 gap-2">
                                            <x-input name="dose" label="Dosis" />
                                            <x-input name="route" label="Vía" />
                                            <x-input name="frequency" label="Frecuencia" />
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <x-input name="start_at" type="datetime-local" label="Inicio" />
                                            <x-input name="end_at" type="datetime-local" label="Fin" />
                                        </div>
                                        <x-textarea name="instructions" label="Instrucciones" />
                                        <input type="hidden" name="day_id" value="{{ $day->id }}" />
                                        <input type="hidden" name="created_by" value="{{ auth()->id() }}" />
                                        <x-button type="submit" size="sm">Agregar orden</x-button>
                                    </form>
                                </div>
                            </x-card>

                            <x-card class="bg-white border border-emerald-100">
                                <h4 class="font-semibold text-emerald-700 mb-2">Aplicaciones del día</h4>
                                <div class="space-y-2">
                                    @foreach($day->administrations as $admin)
                                        <div class="flex items-center justify-between p-2 rounded border border-emerald-100">
                                            <div>
                                                <p class="text-sm font-semibold text-emerald-700">{{ optional($admin->administered_at)->format('H:i') }}</p>
                                                <p class="text-xs text-gray-500">{{ $admin->dose_given }} • {{ $admin->status }}</p>
                                            </div>
                                            <p class="text-xs text-gray-500">{{ $admin->notes }}</p>
                                        </div>
                                    @endforeach
                                    @if($day->administrations->isEmpty())
                                        <p class="text-sm text-gray-500">Sin aplicaciones registradas.</p>
                                    @endif
                                </div>
                            </x-card>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <x-card class="bg-white border border-emerald-100">
                                <h4 class="font-semibold text-emerald-700 mb-2">Signos vitales</h4>
                                <form method="POST" action="{{ route('hospital.vitals.store', $stay) }}" class="grid grid-cols-2 gap-2 mb-3">
                                    @csrf
                                    <x-input name="measured_at" type="datetime-local" label="Fecha" />
                                    <x-input name="temp" label="Temp" />
                                    <x-input name="hr" label="FC" />
                                    <x-input name="rr" label="FR" />
                                    <x-input name="spo2" label="SpO2" />
                                    <x-input name="bp" label="PA" />
                                    <x-input name="weight" label="Peso" />
                                    <x-input name="pain_scale" label="Dolor" />
                                    <x-input name="hydration" label="Hidratación" />
                                    <x-input name="mucous" label="Mucosas" />
                                    <x-input name="crt" label="CRT" />
                                    <x-textarea name="notes" label="Notas" class="col-span-2" />
                                    <input type="hidden" name="measured_by" value="{{ auth()->id() }}" />
                                    <x-button type="submit" size="sm" class="col-span-2">Registrar</x-button>
                                </form>
                                <div class="space-y-2">
                                    @foreach($day->vitals as $vital)
                                        <div class="flex items-center justify-between border border-emerald-100 rounded p-2">
                                            <p class="text-sm text-emerald-700">{{ $vital->measured_at->format('d/m H:i') }}</p>
                                            <p class="text-xs text-gray-500">T: {{ $vital->temp }} • FC: {{ $vital->hr }} • FR: {{ $vital->rr }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </x-card>

                            <x-card class="bg-white border border-emerald-100">
                                <h4 class="font-semibold text-emerald-700 mb-2">Evolución / Progreso</h4>
                                <form method="POST" action="{{ route('hospital.progress.store', $stay) }}" class="space-y-2">
                                    @csrf
                                    <x-select name="shift" label="Turno">
                                        <option value="manana">Mañana</option>
                                        <option value="tarde">Tarde</option>
                                        <option value="noche">Noche</option>
                                    </x-select>
                                    <x-textarea name="content" label="Nota" />
                                    <x-input type="hidden" name="logged_at" value="{{ now()->format('Y-m-d\TH:i') }}" />
                                    <x-input type="hidden" name="author_id" value="{{ auth()->id() }}" />
                                    <x-button type="submit" size="sm">Guardar</x-button>
                                </form>
                                <div class="mt-3 space-y-2">
                                    @foreach($day->progressNotes as $note)
                                        <div class="border border-emerald-100 rounded p-2">
                                            <p class="text-xs text-gray-500">{{ $note->logged_at->format('d/m H:i') }} - {{ ucfirst($note->shift) }}</p>
                                            <p class="text-sm text-emerald-700">{{ $note->content }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </x-card>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="space-y-3 lg:col-span-2 text-sm">
            <x-card class="bg-white border border-emerald-100">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-semibold text-emerald-700">Cargos / Facturación</h4>
                    <span class="text-sm text-gray-500">Subtotal ${{ number_format($stay->charges->sum('total'),2) }}</span>
                </div>
                <div class="space-y-2">
                    @foreach($stay->charges as $charge)
                        <div class="flex items-center justify-between border border-emerald-100 rounded p-2">
                            <div>
                                <p class="text-sm font-semibold text-emerald-700">{{ $charge->description }}</p>
                                <p class="text-xs text-gray-500">{{ $charge->qty }} x ${{ $charge->unit_price }}</p>
                            </div>
                            <p class="text-sm text-emerald-700">${{ $charge->total }}</p>
                        </div>
                    @endforeach
                </div>
                <form method="POST" action="{{ route('hospital.charges.store', $stay) }}" class="mt-3 space-y-2">
                    @csrf
                    <x-select name="source" label="Tipo">
                        <option value="service">Servicio</option>
                        <option value="inventory">Inventario</option>
                        <option value="manual">Manual</option>
                    </x-select>
                    <x-input name="product_id" label="Producto (id)" />
                    <x-input name="description" label="Descripción" />
                    <div class="grid grid-cols-2 gap-2">
                        <x-input name="qty" label="Cantidad" value="1" />
                        <x-input name="unit_price" label="Precio" value="0" />
                    </div>
                    <input type="hidden" name="created_by" value="{{ auth()->id() }}" />
                    <x-button type="submit" size="sm">Agregar ítem</x-button>
                </form>
            </x-card>
        </div>
    </div>
</div>
@endsection
