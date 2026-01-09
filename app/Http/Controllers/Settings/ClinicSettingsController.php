<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateClinicaRequest;
use App\Support\ClinicaActual;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ClinicSettingsController extends Controller
{
    public function edit(): View
    {
        Gate::authorize('manage-clinic-settings');

        return view('settings.clinica', [
            'clinica' => ClinicaActual::get(),
        ]);
    }

    public function update(UpdateClinicaRequest $request): RedirectResponse
    {
        $clinica = ClinicaActual::get();

        $data = $request->validated();
        $data['responsable_iva'] = $request->boolean('responsable_iva');
        $data['dian_enabled'] = $request->boolean('dian_enabled');
        $data['nombre'] = $data['name'];
        $data['direccion'] = $data['address'] ?? null;
        $data['terminos'] = $data['payment_terms'] ?? null;

        $clinica->fill($data);
        $clinica->save();

        return back()->with('status', 'Configuración actualizada correctamente.');
    }

    public function uploadLogo(Request $request): RedirectResponse
    {
        Gate::authorize('manage-clinic-settings');

        $validated = $request->validate([
            'logo' => ['required', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
        ]);

        $clinica = ClinicaActual::get();

        if ($clinica->logo_path) {
            Storage::disk('public')->delete($clinica->logo_path);
        }

        $extension = $validated['logo']->getClientOriginalExtension();
        $path = $validated['logo']->storeAs(
            "clinicas/{$clinica->id}",
            "logo.{$extension}",
            'public'
        );

        $clinica->update(['logo_path' => $path]);

        return back()->with('status', 'Logo actualizado correctamente.');
    }

    public function removeLogo(): RedirectResponse
    {
        Gate::authorize('manage-clinic-settings');

        $clinica = ClinicaActual::get();

        if ($clinica->logo_path) {
            Storage::disk('public')->delete($clinica->logo_path);
        }

        $clinica->update(['logo_path' => null]);

        return back()->with('status', 'Logo eliminado correctamente.');
    }
}
