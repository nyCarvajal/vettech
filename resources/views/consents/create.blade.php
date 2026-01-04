@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6 space-y-4">
    <h1 class="text-2xl font-bold">Generar consentimiento</h1>
    <form method="POST" action="{{ route('consents.store') }}" class="space-y-4">
        @csrf
        @if($patient)
            <input type="hidden" name="patient_id" value="{{ $patient->id }}">
        @endif
        <div>
            <label class="block text-sm font-medium">Plantilla</label>
            <select name="template_id" class="mt-1 w-full border rounded px-3 py-2">
                @foreach($templates as $template)
                <option value="{{ $template->id }}">{{ $template->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <h2 class="font-semibold mb-2">Tutor</h2>
                @foreach(['first_name'=>'Nombre','last_name'=>'Apellido','document'=>'Documento','phone'=>'Teléfono','email'=>'Email'] as $field=>$label)
                <label class="block text-sm">{{ $label }}
                    <input type="text" name="owner_snapshot[{{ $field }}]" value="{{ $ownerSnapshot[$field] ?? '' }}" class="mt-1 w-full border rounded px-3 py-2" />
                </label>
                @endforeach
            </div>
            <div>
                <h2 class="font-semibold mb-2">Paciente</h2>
                @foreach(['name'=>'Nombre','species'=>'Especie','breed'=>'Raza','age'=>'Edad','weight'=>'Peso'] as $field=>$label)
                <label class="block text-sm">{{ $label }}
                    <input type="text" name="pet_snapshot[{{ $field }}]" value="{{ $petSnapshot[$field] ?? '' }}" class="mt-1 w-full border rounded px-3 py-2" />
                </label>
                @endforeach
            </div>
        </div>
        <div class="flex justify-end">
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Generar</button>
        </div>
    </form>
</div>
@endsection
