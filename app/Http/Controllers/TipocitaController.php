<?php

namespace App\Http\Controllers;

use App\Models\Tipocita;
use Illuminate\Http\Request;

class TipocitaController extends Controller
{
    public function index()
    {
        $tipocitas = Tipocita::orderBy('nombre')->paginate(10);

        return view('tipocitas.index', compact('tipocitas'));
    }

    public function create()
    {
        return view('tipocitas.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string|max:255',
            'duracion' => 'nullable|integer|min:0',
        ]);

        Tipocita::create($data);

        return redirect()
            ->route('tipocitas.index')
            ->with('success', 'Tipo de cita creado correctamente.');
    }

    public function edit(Tipocita $tipocita)
    {
        return view('tipocitas.edit', compact('tipocita'));
    }

    public function update(Request $request, Tipocita $tipocita)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string|max:255',
            'duracion' => 'nullable|integer|min:0',
        ]);

        $tipocita->update($data);

        return redirect()
            ->route('tipocitas.index')
            ->with('success', 'Tipo de cita actualizado correctamente.');
    }

    public function destroy(Tipocita $tipocita)
    {
        $tipocita->delete();

        return redirect()
            ->route('tipocitas.index')
            ->with('success', 'Tipo de cita eliminado correctamente.');
    }
}
