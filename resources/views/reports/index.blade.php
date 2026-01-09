@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900">Informes administrativos</h1>
        <p class="text-sm text-gray-500">Selecciona una sección para ver indicadores avanzados.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        <a href="{{ route('reports.sales') }}" class="bg-white border border-gray-200 rounded-xl p-4 shadow-soft hover:border-mint-300">
            <h2 class="font-semibold">Ventas y facturación</h2>
            <p class="text-sm text-gray-500">KPIs, comparativos y top productos.</p>
        </a>
        <a href="{{ route('reports.payments') }}" class="bg-white border border-gray-200 rounded-xl p-4 shadow-soft hover:border-mint-300">
            <h2 class="font-semibold">Pagos y cartera</h2>
            <p class="text-sm text-gray-500">Métodos de pago y cuentas por cobrar.</p>
        </a>
        <a href="{{ route('reports.expenses') }}" class="bg-white border border-gray-200 rounded-xl p-4 shadow-soft hover:border-mint-300">
            <h2 class="font-semibold">Gastos y rentabilidad</h2>
            <p class="text-sm text-gray-500">Egresos y utilidad estimada.</p>
        </a>
        <a href="{{ route('reports.cash') }}" class="bg-white border border-gray-200 rounded-xl p-4 shadow-soft hover:border-mint-300">
            <h2 class="font-semibold">Caja y arqueo</h2>
            <p class="text-sm text-gray-500">Ingresos vs egresos diarios.</p>
        </a>
        <a href="{{ route('reports.operations') }}" class="bg-white border border-gray-200 rounded-xl p-4 shadow-soft hover:border-mint-300">
            <h2 class="font-semibold">Operación</h2>
            <p class="text-sm text-gray-500">Productividad y horas pico.</p>
        </a>
        <a href="{{ route('reports.grooming') }}" class="bg-white border border-gray-200 rounded-xl p-4 shadow-soft hover:border-mint-300">
            <h2 class="font-semibold">Peluquería</h2>
            <p class="text-sm text-gray-500">Servicios y clientes frecuentes.</p>
        </a>
        <a href="{{ route('reports.inventory') }}" class="bg-white border border-gray-200 rounded-xl p-4 shadow-soft hover:border-mint-300">
            <h2 class="font-semibold">Inventario</h2>
            <p class="text-sm text-gray-500">Kardex, rotación y stock crítico.</p>
        </a>
    </div>
</div>
@endsection
