<?php

namespace App\Http\Controllers;

use App\Models\Clinica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClinicaController extends Controller
{
    public function index()
    {
        $clinicas = Clinica::orderBy('nombre')->paginate(20);

        return view('clinicas.index', compact('clinicas'));
    }

    public function create()
    {
        return view('clinicas.create', ['clinica' => new Clinica()]);
    }

    public function store(Request $request)
    {
        $clinica = Clinica::create($this->validated($request));

        return redirect()
            ->route('clinicas.index')
            ->with('success', 'Clínica creada correctamente.');
    }

    public function show(Clinica $clinica)
    {
        return view('clinicas.show', compact('clinica'));
    }

    public function edit(Clinica $clinica)
    {
        return view('clinicas.edit', compact('clinica'));
    }

    public function update(Request $request, Clinica $clinica)
    {
        $clinica->update($this->validated($request, $clinica->id));

        return redirect()
            ->route('clinicas.edit', $clinica)
            ->with('success', 'Clínica actualizada correctamente.');
    }

    public function destroy(Clinica $clinica)
    {
        $clinica->delete();

        return redirect()
            ->route('clinicas.index')
            ->with('success', 'Clínica eliminada correctamente.');
    }

    public function editOwn()
    {
        $clinica = Auth::user()?->peluqueria;

        abort_unless($clinica, 404);

        return view('clinicas.edit', compact('clinica'));
    }

    public function updateOwn(Request $request)
    {
        $clinica = Auth::user()?->peluqueria;

        abort_unless($clinica, 404);

        $clinica->update($this->validated($request, $clinica->id));

        return redirect()
            ->route('clinicas.edit')
            ->with('success', 'Clínica actualizada correctamente.');
    }

    public function showOwn()
    {
        $clinica = Auth::user()?->peluqueria;

        abort_unless($clinica, 404);

        return view('clinicas.show', compact('clinica'));
    }

    private function validated(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:300'],
            'slug' => ['nullable', 'string', 'max:191', 'unique:clinicas,slug,' . ($id ?? 'null')],
            'email' => ['nullable', 'email', 'max:191'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:100'],
            'menu_color' => ['nullable', 'string', 'max:20'],
            'topbar_color' => ['nullable', 'string', 'max:20'],
            'msj_bienvenida' => ['nullable', 'string', 'max:500'],
            'msj_reserva_confirmada' => ['nullable', 'string', 'max:500'],
            'msj_finalizado' => ['nullable', 'string', 'max:500'],
            'trainer_label_singular' => ['nullable', 'string', 'max:191'],
            'trainer_label_plural' => ['nullable', 'string', 'max:191'],
            'nit' => ['nullable', 'string', 'max:100'],
            'direccion' => ['nullable', 'string', 'max:300'],
            'municipio' => ['nullable', 'integer'],
            'db' => ['nullable', 'string', 'max:191'],
        ]);
    }
}
