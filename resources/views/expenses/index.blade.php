@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Gastos</h1>
            <p class="text-sm text-gray-500">Listado de gastos registrados.</p>
        </div>
        <a href="{{ route('expenses.create') }}" class="bg-mint-600 text-white px-4 py-2 rounded">Nuevo gasto</a>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-soft">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left">Fecha</th>
                    <th class="px-4 py-3 text-left">Categoría</th>
                    <th class="px-4 py-3 text-left">Descripción</th>
                    <th class="px-4 py-3 text-left">Monto</th>
                    <th class="px-4 py-3 text-left">Método</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($expenses as $expense)
                    <tr>
                        <td class="px-4 py-3">{{ $expense->paid_at?->format('Y-m-d') }}</td>
                        <td class="px-4 py-3">{{ $expense->category }}</td>
                        <td class="px-4 py-3">{{ $expense->description }}</td>
                        <td class="px-4 py-3">{{ '$' . number_format($expense->amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">{{ $expense->payment_method }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('expenses.edit', $expense) }}" class="text-mint-600">Editar</a>
                            <form method="POST" action="{{ route('expenses.destroy', $expense) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600" onclick="return confirm('¿Eliminar gasto?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-4 py-6 text-center text-gray-500" colspan="6">Sin gastos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $expenses->links() }}
</div>
@endsection
