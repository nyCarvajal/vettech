@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-6xl space-y-6 px-4 py-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Historial de cierres</h1>
            <p class="text-sm text-gray-500">Consulta cierres diarios y revisa diferencias.</p>
        </div>
        <a href="{{ route('cash.closures.create') }}" class="rounded-lg bg-mint-600 px-4 py-2 text-sm font-semibold text-white">Nuevo cierre</a>
    </div>

    <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
        <form class="flex flex-wrap items-end gap-3" method="GET" action="{{ route('cash.closures.index') }}">
            <div>
                <label class="text-xs font-semibold uppercase text-gray-400">Desde</label>
                <input type="date" name="from" value="{{ request('from') }}" class="mt-1 block rounded-lg border border-gray-200 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="text-xs font-semibold uppercase text-gray-400">Hasta</label>
                <input type="date" name="to" value="{{ request('to') }}" class="mt-1 block rounded-lg border border-gray-200 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="text-xs font-semibold uppercase text-gray-400">Usuario (ID)</label>
                <input type="number" name="user_id" value="{{ request('user_id') }}" class="mt-1 block rounded-lg border border-gray-200 px-3 py-2 text-sm" placeholder="Ej: 12">
            </div>
            <button class="rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-600">Filtrar</button>
        </form>
    </div>

    <div class="rounded-2xl border border-gray-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="border-b border-gray-100 text-left text-xs uppercase text-gray-400">
                    <tr>
                        <th class="px-4 py-3">Fecha</th>
                        <th class="px-4 py-3">Usuario</th>
                        <th class="px-4 py-3 text-right">Esperado</th>
                        <th class="px-4 py-3 text-right">Contado</th>
                        <th class="px-4 py-3 text-right">Diferencia</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($closures as $closure)
                        <tr class="text-gray-700">
                            <td class="px-4 py-3">{{ $closure->date->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">{{ $closure->user->nombre ?? $closure->user->name ?? 'Usuario #' . $closure->user_id }}</td>
                            <td class="px-4 py-3 text-right">$ {{ number_format($closure->total_expected, 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right">$ {{ number_format($closure->total_counted, 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right">
                                <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $closure->difference == 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                    $ {{ number_format($closure->difference, 2, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('cash.closures.show', $closure) }}" class="text-sm font-medium text-mint-600">Ver</a>
                                <a href="{{ route('cash.closures.print', $closure) }}" target="_blank" class="ml-3 text-sm font-medium text-gray-500">Imprimir</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">No hay cierres registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3">
            {{ $closures->links() }}
        </div>
    </div>
</div>
@endsection
