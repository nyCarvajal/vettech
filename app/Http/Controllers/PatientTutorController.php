<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PatientTutorController extends Controller
{
    public function store(Request $request, Patient $patient): RedirectResponse
    {
        $data = $request->validate([
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['nullable', 'string', 'max:100'],
            'documento' => ['nullable', 'string', 'max:50'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'observaciones' => ['nullable', 'string'],
            'parentesco' => ['nullable', 'string', 'max:100'],
            'es_principal' => ['nullable', 'boolean'],
        ]);

        $owner = Owner::create([
            'name' => trim(($data['nombres'] ?? '') . ' ' . ($data['apellidos'] ?? '')),
            'document' => $data['documento'] ?? null,
            'phone' => $data['telefono'] ?? null,
            'whatsapp' => $data['whatsapp'] ?? null,
            'email' => $data['email'] ?? null,
            'address' => $data['direccion'] ?? null,
            'city' => $data['ciudad'] ?? null,
            'notes' => $data['observaciones'] ?? null,
        ]);

        $isPrimary = (bool) ($data['es_principal'] ?? false);

        $patient->tutors()->syncWithoutDetaching([
            $owner->id => [
                'relationship' => $data['parentesco'] ?? null,
                'is_primary' => $isPrimary,
            ],
        ]);

        if ($isPrimary) {
            $this->markPrimary($patient, $owner);
        }

        return back()->with('status', 'Tutor agregado correctamente.');
    }

    public function attach(Request $request, Patient $patient): RedirectResponse
    {
        $data = $request->validate([
            'owner_id' => ['required', 'exists:owners,id'],
            'parentesco' => ['nullable', 'string', 'max:100'],
            'es_principal' => ['nullable', 'boolean'],
        ]);

        $isPrimary = (bool) ($data['es_principal'] ?? false);

        $patient->tutors()->syncWithoutDetaching([
            $data['owner_id'] => [
                'relationship' => $data['parentesco'] ?? null,
                'is_primary' => $isPrimary,
            ],
        ]);

        if ($isPrimary) {
            $owner = Owner::findOrFail($data['owner_id']);
            $this->markPrimary($patient, $owner);
        }

        return back()->with('status', 'Tutor asociado correctamente.');
    }

    public function detach(Patient $patient, Owner $owner): RedirectResponse
    {
        $patient->tutors()->detach($owner->id);

        if ($patient->owner_id === $owner->id) {
            $newPrimary = $patient->tutors()->first();
            $patient->update(['owner_id' => $newPrimary?->id]);
        }

        return back()->with('status', 'Tutor removido correctamente.');
    }

    public function setPrimary(Patient $patient, Owner $owner): RedirectResponse
    {
        $this->markPrimary($patient, $owner);

        return back()->with('status', 'Tutor principal actualizado.');
    }

    private function markPrimary(Patient $patient, Owner $owner): void
    {
        $patient->tutors()->updateExistingPivot($owner->id, ['is_primary' => true]);
        $patient->tutors()->wherePivot('owner_id', '!=', $owner->id)->update(['is_primary' => false]);
        $patient->update(['owner_id' => $owner->id]);
    }
}
