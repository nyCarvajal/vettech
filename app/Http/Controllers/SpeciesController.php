<?php

namespace App\Http\Controllers;

use App\Models\Species;
use Illuminate\Http\Request;

class SpeciesController extends Controller
{
    public function index()
    {
        $species = Species::orderBy('name')->paginate(10);

        return view('especies.index', compact('species'));
    }

    public function create()
    {
        return view('especies.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
        ]);

        Species::create($data);

        return redirect()
            ->route('especies.index')
            ->with('success', 'Especie creada correctamente.');
    }

    public function edit(Species $especie)
    {
        return view('especies.edit', compact('especie'));
    }

    public function update(Request $request, Species $especie)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
        ]);

        $especie->update($data);

        return redirect()
            ->route('especies.index')
            ->with('success', 'Especie actualizada correctamente.');
    }

    public function destroy(Species $especie)
    {
        $especie->delete();

        return redirect()
            ->route('especies.index')
            ->with('success', 'Especie eliminada correctamente.');
    }
}
