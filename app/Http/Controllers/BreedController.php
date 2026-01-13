<?php

namespace App\Http\Controllers;

use App\Models\Breed;
use App\Models\Species;
use Illuminate\Http\Request;

class BreedController extends Controller
{
    public function index()
    {
        $breeds = Breed::with('species')
            ->orderBy('name')
            ->paginate(10);

        return view('razas.index', compact('breeds'));
    }

    public function create()
    {
        $species = Species::orderBy('name')->get();

        return view('razas.create', compact('species'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'species_id' => 'required|integer|exists:species,id',
        ]);

        Breed::create($data);

        return redirect()
            ->route('razas.index')
            ->with('success', 'Raza creada correctamente.');
    }

    public function edit(Breed $raza)
    {
        $species = Species::orderBy('name')->get();

        return view('razas.edit', compact('raza', 'species'));
    }

    public function update(Request $request, Breed $raza)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'species_id' => 'required|integer|exists:species,id',
        ]);

        $raza->update($data);

        return redirect()
            ->route('razas.index')
            ->with('success', 'Raza actualizada correctamente.');
    }

    public function destroy(Breed $raza)
    {
        $raza->delete();

        return redirect()
            ->route('razas.index')
            ->with('success', 'Raza eliminada correctamente.');
    }
}
