@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ advanced: false }">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <p class="text-sm text-gray-500">Seguimientos posteriores a consultas</p>
            <h1 class="text-2xl font-bold text-gray-900">Controles</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('followups.create') }}" class="pill-action">Nuevo control</a>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
        <form method="get" class="grid gap-3 md:grid-cols-5">
            <div class="md:col-span-2">
                <label class="form-label text-muted">Paciente</label>
                <select name="patient_id" class="form-control">
                    <option value="">Todos</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}" @selected(request('patient_id') == $patient->id)>{{ $patient->display_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label text-muted">Estado</label>
                <select name="improved_status" class="form-control">
                    <option value="">Todos</option>
                    @foreach(['yes' => 'Sí', 'partial' => 'Parcial', 'no' => 'No', 'unknown' => 'No sabe'] as $key => $label)
                        <option value="{{ $key }}" @selected(request('improved_status') === $key)> {{ $label }} </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label text-muted">Desde</label>
                <input type="date" name="from" value="{{ request('from') }}" class="form-control">
            </div>
            <div>
                <label class="form-label text-muted">Hasta</label>
                <input type="date" name="to" value="{{ request('to') }}" class="form-control">
            </div>
            <div class="md:col-span-5 flex justify-end">
                <button class="pill-action" type="submit">Filtrar</button>
            </div>
        </form>
    </div>

    <div class="rounded-2xl border border-gray-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="table table-hover mb-0">
                <thead class="bg-gray-50">
                    <tr>
                        <th>Código</th>
                        <th>Paciente</th>
                        <th>Fecha</th>
                        <th>Mejoró</th>
                        <th>Responsable</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($followups as $followup)
                        <tr>
                            <td class="font-semibold text-gray-900">{{ $followup->code }}</td>
                            <td>{{ $followup->patient->display_name ?? 'N/D' }}</td>
                            <td>{{ optional($followup->followup_at)->format('d/m/Y H:i') }}</td>
                            <td class="text-sm capitalize">{{ $followup->improved_status }}</td>
                            <td>{{ $followup->performed_by ?? '—' }}</td>
                            <td class="text-end">
                                <a href="{{ route('followups.show', $followup) }}" class="text-mint-600 font-semibold">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-6 text-gray-500">No hay controles registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $followups->links() }}</div>
    </div>
</div>
@endsection
