@props([
    'filters',
    'action' => null,
    'users' => collect(),
    'owners' => collect(),
    'paymentMethods' => [],
    'showGranularity' => true,
    'showUser' => true,
    'showOwner' => true,
    'showPaymentMethod' => false,
])

@php
    $range = request('range', '30d');
    $customVisible = $range === 'custom' || request()->filled(['from', 'to']);
@endphp

<form method="GET" action="{{ $action ?? url()->current() }}" class="bg-white border border-gray-200 rounded-xl p-4 space-y-4" x-data="{ range: '{{ $range }}', custom: {{ $customVisible ? 'true' : 'false' }} }">
    <div class="flex flex-wrap gap-4 items-end">
        <div class="flex flex-col gap-1">
            <label class="text-sm text-gray-600">Rango</label>
            <select name="range" class="border border-gray-300 rounded px-3 py-2" x-model="range" @change="custom = (range === 'custom')">
                <option value="today" @selected($range === 'today')>Hoy</option>
                <option value="7d" @selected($range === '7d')>Últimos 7 días</option>
                <option value="30d" @selected($range === '30d')>Últimos 30 días</option>
                <option value="this_month" @selected($range === 'this_month')>Este mes</option>
                <option value="last_month" @selected($range === 'last_month')>Mes anterior</option>
                <option value="custom" @selected($range === 'custom')>Personalizado</option>
            </select>
        </div>
        <div class="flex flex-col gap-1" x-show="custom">
            <label class="text-sm text-gray-600">Desde</label>
            <input type="date" name="from" value="{{ $filters->from->format('Y-m-d') }}" class="border border-gray-300 rounded px-3 py-2" />
        </div>
        <div class="flex flex-col gap-1" x-show="custom">
            <label class="text-sm text-gray-600">Hasta</label>
            <input type="date" name="to" value="{{ $filters->to->format('Y-m-d') }}" class="border border-gray-300 rounded px-3 py-2" />
        </div>
        @if($showGranularity)
            <div class="flex flex-col gap-1">
                <label class="text-sm text-gray-600">Agrupación</label>
                <select name="granularity" class="border border-gray-300 rounded px-3 py-2">
                    <option value="day" @selected($filters->granularity === 'day')>Día</option>
                    <option value="week" @selected($filters->granularity === 'week')>Semana</option>
                    <option value="month" @selected($filters->granularity === 'month')>Mes</option>
                </select>
            </div>
        @endif
        @if($showUser)
            <div class="flex flex-col gap-1">
                <label class="text-sm text-gray-600">Usuario</label>
                <select name="user_id" class="border border-gray-300 rounded px-3 py-2">
                    <option value="">Todos</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        @if($showOwner)
            <div class="flex flex-col gap-1">
                <label class="text-sm text-gray-600">Cliente</label>
                <select name="owner_id" class="border border-gray-300 rounded px-3 py-2">
                    <option value="">Todos</option>
                    @foreach($owners as $owner)
                        <option value="{{ $owner->id }}" @selected(request('owner_id') == $owner->id)>{{ $owner->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        @if($showPaymentMethod)
            <div class="flex flex-col gap-1">
                <label class="text-sm text-gray-600">Método de pago</label>
                <select name="payment_method" class="border border-gray-300 rounded px-3 py-2">
                    <option value="">Todos</option>
                    @foreach($paymentMethods as $method)
                        <option value="{{ $method }}" @selected(request('payment_method') === $method)>{{ ucfirst($method) }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <div class="flex gap-2">
            <button type="submit" class="bg-mint-600 text-white px-4 py-2 rounded">Aplicar</button>
            <a href="{{ url()->current() }}" class="border border-gray-300 px-4 py-2 rounded">Limpiar</a>
        </div>
    </div>
</form>
