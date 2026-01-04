@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold text-gray-800">Procedimientos</h1>
        <a href="{{ route('procedures.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded shadow">Nuevo</a>
    </div>

    <form method="GET" class="bg-white p-4 rounded shadow mb-6 grid grid-cols-1 md:grid-cols-5 gap-4">
        <input type="text" name="responsible_vet_name" value="{{ request('responsible_vet_name') }}" placeholder="Responsable" class="input input-bordered w-full" />
        <select name="type" class="input input-bordered w-full">
            <option value="">Tipo</option>
            <option value="surgery" @selected(request('type')==='surgery')>Cirugía</option>
            <option value="procedure" @selected(request('type')==='procedure')>Procedimiento</option>
        </select>
        <select name="status" class="input input-bordered w-full">
            <option value="">Estado</option>
            @foreach(['scheduled'=>'Programado','in_progress'=>'En curso','completed'=>'Completado','canceled'=>'Cancelado'] as $value=>$label)
            <option value="{{ $value }}" @selected(request('status')===$value)>{{ $label }}</option>
            @endforeach
        </select>
        <input type="date" name="from" value="{{ request('from') }}" class="input input-bordered w-full" />
        <input type="date" name="to" value="{{ request('to') }}" class="input input-bordered w-full" />
        <div class="md:col-span-5 flex justify-end">
            <button class="bg-gray-800 text-white px-4 py-2 rounded">Filtrar</button>
        </div>
    </form>

    <div class="bg-white shadow rounded overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left">Código</th>
                    <th class="px-4 py-2 text-left">Paciente</th>
                    <th class="px-4 py-2 text-left">Procedimiento</th>
                    <th class="px-4 py-2 text-left">Fecha</th>
                    <th class="px-4 py-2 text-left">Estado</th>
                    <th class="px-4 py-2 text-left">Consentimiento</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($procedures as $procedure)
                <tr class="border-t">
                    <td class="px-4 py-2 font-medium">{{ $procedure->code }}</td>
                    <td class="px-4 py-2">{{ $procedure->patient_snapshot['name'] ?? 'Paciente' }}</td>
                    <td class="px-4 py-2">{{ $procedure->name }}</td>
                    <td class="px-4 py-2">{{ optional($procedure->scheduled_at)->format('Y-m-d H:i') }}</td>
                    <td class="px-4 py-2 capitalize">{{ str_replace('_', ' ', $procedure->status) }}</td>
                    <td class="px-4 py-2">{{ $procedure->consent_document_id ? 'Sí' : 'No' }}</td>
                    <td class="px-4 py-2 text-right space-x-2">
                        <a href="{{ route('procedures.show', $procedure) }}" class="text-indigo-600">Ver</a>
                        <a href="{{ route('procedures.edit', $procedure) }}" class="text-gray-600">Editar</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-4 text-center text-gray-500">Sin resultados</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $procedures->links() }}</div>
</div>
@endsection
