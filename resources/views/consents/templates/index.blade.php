@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Plantillas de consentimientos</h1>
        <a href="{{ route('consent-templates.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded">Nueva plantilla</a>
    </div>

    <div class="bg-white shadow rounded">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-4 py-2">Categoría</th>
                    <th class="px-4 py-2">Estado</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($templates as $template)
                <tr>
                    <td class="px-4 py-2">{{ $template->name }}</td>
                    <td class="px-4 py-2">{{ $template->category }}</td>
                    <td class="px-4 py-2">{{ $template->is_active ? 'Activo' : 'Inactivo' }}</td>
                    <td class="px-4 py-2 text-right space-x-2">
                        <a href="{{ route('consent-templates.show', $template) }}" class="text-indigo-600">Ver</a>
                        <a href="{{ route('consent-templates.edit', $template) }}" class="text-indigo-600">Editar</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">
            {{ $templates->links() }}
        </div>
    </div>
</div>
@endsection
