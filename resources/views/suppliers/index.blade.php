@extends('layouts.app', ['subtitle' => 'Proveedores'])
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Proveedores</h3>
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary">Nuevo proveedor</a>
    </div>
    <form class="card card-body mb-3" method="GET">
        <div class="row g-2">
            <div class="col-md-6"><input class="form-control" name="search" value="{{ $search }}" placeholder="Buscar por nombre, documento o teléfono"></div>
            <div class="col-md-2"><button class="btn btn-outline-secondary w-100">Buscar</button></div>
        </div>
    </form>
    <div class="card"><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Proveedor</th><th>Documento</th><th>Contacto</th><th>Estado</th><th></th></tr></thead><tbody>
        @forelse($suppliers as $supplier)
            <tr>
                <td>{{ $supplier->razon_social }}</td><td>{{ $supplier->tipo_documento }} {{ $supplier->numero_documento }}</td><td>{{ $supplier->telefono }} / {{ $supplier->email }}</td><td>{{ ucfirst($supplier->estado) }}</td>
                <td class="text-end"><a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-sm btn-outline-primary">Ver</a> <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-outline-secondary">Editar</a></td>
            </tr>
        @empty <tr><td colspan="5" class="text-center text-muted">Sin resultados</td></tr> @endforelse
    </tbody></table></div></div>
    <div class="mt-2">{{ $suppliers->links() }}</div>
</div>
@endsection
