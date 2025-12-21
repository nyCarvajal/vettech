<?php

namespace App\Http\Controllers;

use App\Models\Departamentos;
use App\Models\Municipios;
use App\Models\Paciente;
use App\Models\TipoIdentificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ClientesController extends Controller
{
    /**
     * Display a listing of the patients.
     */
    public function index()
    {
        $pacientes = Paciente::orderBy('nombres')->paginate(15);

        return view('pacientes.index', compact('pacientes'));
    }

    /**
     * Show the form for creating a new patient.
     */
    public function create()
    {
        $tiposDocumento = TipoIdentificacion::all();
        $departamentos = Departamentos::all();
        $municipios = Municipios::all();

        return view('pacientes.create', compact('tiposDocumento', 'departamentos', 'municipios'));
    }

    /**
     * Store a newly created patient in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo_documento_id' => ['nullable', 'exists:tipo_identificacions,id'],
            'numero_documento' => ['nullable', 'string', 'max:100'],
            'nombres' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'departamento_id' => ['nullable', 'exists:departamentos,id'],
            'municipio_id' => ['nullable', 'exists:municipios,id'],
            'whatsapp' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'sexo' => ['nullable', 'in:hombre,mujer,otro'],
            'fecha_nacimiento' => ['nullable', 'date'],
            'alergias' => ['nullable', 'string'],
            'acompanante' => ['nullable', 'string', 'max:255'],
            'acompanante_contacto' => ['nullable', 'string', 'max:50'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $data['tipo_documento'] = optional(TipoIdentificacion::find($request->integer('tipo_documento_id')))->tipo;
        $data['ciudad'] = optional(Municipios::find($request->integer('municipio_id')))->nombre;

        unset($data['departamento_id'], $data['municipio_id'], $data['tipo_documento_id']);

        $paciente = Paciente::create($data);

        return redirect()
            ->route('pacientes.show', $paciente)
            ->with('status', 'Paciente creado correctamente.');
    }

    /**
     * Display the specified patient.
     */
    public function show(Paciente $paciente)
    {
        $historiaReciente = $paciente->historiasClinicas()
            ->with(['diagnosticos', 'paraclinicos'])
            ->latest('updated_at')
            ->first();

        $edad = $paciente->fecha_nacimiento ? Carbon::parse($paciente->fecha_nacimiento)->age : null;

        $sugerencias = [];

        if (! $historiaReciente) {
            $sugerencias[] = 'Crear la primera historia clínica para capturar signos vitales y antecedentes.';
        }

        if (! $paciente->email) {
            $sugerencias[] = 'Agregar un correo electrónico para enviar recordatorios y estudios pendientes.';
        }

        if (! $paciente->direccion || ! $paciente->ciudad) {
            $sugerencias[] = 'Completar dirección y ciudad para coordinar visitas domiciliarias o entregas.';
        }

        if (! $paciente->acompanante || ! $paciente->acompanante_contacto) {
            $sugerencias[] = 'Registrar el acompañante y su número de contacto para comunicaciones en caso de emergencia.';
        }

        if ($historiaReciente && (! $historiaReciente->tension_arterial || ! $historiaReciente->frecuencia_cardiaca)) {
            $sugerencias[] = 'Registrar tensión arterial y frecuencia cardiaca en la próxima consulta.';
        }

        if ($historiaReciente && ! $historiaReciente->plan_medicamentos) {
            $sugerencias[] = 'Documentar el plan de medicamentos con dosis y frecuencia.';
        }

        if ($historiaReciente && $historiaReciente->paraclinicos->isEmpty()) {
            $sugerencias[] = 'Añadir paraclínicos solicitados o resultados recientes.';
        }

        return view('pacientes.show', compact('paciente', 'historiaReciente', 'edad', 'sugerencias'));
    }

    /**
     * Show the form for editing the specified patient.
     */
    public function edit(Paciente $paciente)
    {
        $tiposDocumento = TipoIdentificacion::all();
        $departamentos = Departamentos::all();
        $municipios = Municipios::all();
        $selectedMunicipioId = $paciente->ciudad
            ? optional($municipios->firstWhere('nombre', $paciente->ciudad))->id
            : null;

        return view('pacientes.edit', compact('paciente', 'tiposDocumento', 'departamentos', 'municipios', 'selectedMunicipioId'));
    }

    /**
     * Update the specified patient in storage.
     */
    public function update(Request $request, Paciente $paciente)
    {
        $data = $request->validate([
            'tipo_documento_id' => ['nullable', 'exists:tipo_identificacions,id'],
            'numero_documento' => ['nullable', 'string', 'max:100'],
            'nombres' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'departamento_id' => ['nullable', 'exists:departamentos,id'],
            'municipio_id' => ['nullable', 'exists:municipios,id'],
            'whatsapp' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'sexo' => ['nullable', 'in:hombre,mujer,otro'],
            'fecha_nacimiento' => ['nullable', 'date'],
            'alergias' => ['nullable', 'string'],
            'acompanante' => ['nullable', 'string', 'max:255'],
            'acompanante_contacto' => ['nullable', 'string', 'max:50'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $data['tipo_documento'] = optional(TipoIdentificacion::find($request->integer('tipo_documento_id')))->tipo;
        $data['ciudad'] = optional(Municipios::find($request->integer('municipio_id')))->nombre;

        unset($data['departamento_id'], $data['municipio_id'], $data['tipo_documento_id']);

        $paciente->update($data);

        return redirect()
            ->route('pacientes.show', $paciente)
            ->with('status', 'Paciente actualizado correctamente.');
    }

    /**
     * Remove the specified patient from storage.
     */
    public function destroy(Paciente $paciente)
    {
        $paciente->delete();

        return redirect()
            ->route('pacientes.index')
            ->with('status', 'Paciente eliminado correctamente.');
    }

    /**
     * Simple search endpoint for autocomplete widgets.
     */
    public function search(Request $request)
    {
        $term = $request->input('q', $request->input('term', ''));

        $results = Paciente::query()
            ->when($term, fn ($query) => $query->where('nombres', 'like', "%{$term}%")
                ->orWhere('apellidos', 'like', "%{$term}%")
                ->orWhere('whatsapp', 'like', "%{$term}%"))
            ->orderBy('nombres')
            ->limit(10)
            ->get(['id', 'nombres', 'apellidos', 'whatsapp']);

        return response()->json($results);
    }

    /**
     * Placeholder view for patient birthdays.
     */
    public function birthdays()
    {
        $cumpleanos = Paciente::whereMonth('fecha_nacimiento', now()->month)
            ->orderByRaw('DAY(fecha_nacimiento) asc')
            ->get();

        return view('pacientes.birthdays', compact('cumpleanos'));
    }

    /**
     * Placeholder action for reengaging patients.
     */
    public function reengage()
    {
        $pacientes = Paciente::orderBy('nombres')->get();

        return view('pacientes.reengage', compact('pacientes'));
    }
}
