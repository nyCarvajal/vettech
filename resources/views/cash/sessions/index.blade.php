@extends('layouts.app')
@section('content')
<div class="container">
    <h1 class="text-xl font-bold mb-4">Caja</h1>
    <form method="post" action="{{ route('cash.sessions.store') }}" class="mb-4">
        @csrf
        <input name="cash_register_id" placeholder="Caja ID" class="border p-2" required>
        <input name="opening_amount" type="number" step="0.01" placeholder="Monto inicial" class="border p-2" required>
        <button class="btn btn-primary">Abrir caja</button>
    </form>

    <table class="table-auto w-full">
        <thead><tr><th>ID</th><th>Estado</th><th>Abierta</th><th>Cierre</th><th>Acciones</th></tr></thead>
        <tbody>
            @foreach($sessions as $session)
                <tr>
                    <td>{{ $session->id }}</td>
                    <td>{{ $session->status }}</td>
                    <td>{{ $session->opened_at }}</td>
                    <td>{{ $session->closed_at }}</td>
                    <td>
                        @if($session->status === 'open')
                        <form method="post" action="{{ route('cash.sessions.close', $session) }}">
                            @csrf
                            <input name="closing_amount_counted" placeholder="Monto contado" class="border p-1" required>
                            <input name="notes" placeholder="Notas" class="border p-1">
                            <button class="btn btn-secondary">Cerrar</button>
                        </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $sessions->links() }}
</div>
@endsection
