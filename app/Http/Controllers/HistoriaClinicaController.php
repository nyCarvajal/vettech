<?php

namespace App\Http\Controllers;

use App\Models\ExamReferral;
use App\Models\HistoriaClinica;
use App\Models\Paciente;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Product;
use App\Services\BillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\PDF;

class HistoriaClinicaController extends Controller
{
    public function index()
    {
        $historias = HistoriaClinica::with('paciente')
            ->latest()
            ->paginate(15);

        return view('historias_clinicas.index', compact('historias'));
    }

    public function create(Request $request)
    {
        $historia = new HistoriaClinica([
            'paciente_id' => $request->integer('paciente_id') ?: null,
        ]);

        return view('historias_clinicas.create', $this->formData($historia));
    }

    public function store(Request $request)
    {
        $validated = $this->validatedData($request);

        $historia = HistoriaClinica::create($validated['fields']);

        $this->syncParaclinicos($historia, $validated['paraclinicos']);
        $this->syncDiagnosticos($historia, $validated['diagnosticos']);

        return redirect()->route('historias-clinicas.show', $historia)
            ->with('success', 'Historia clínica guardada correctamente.');
    }

    public function edit(HistoriaClinica $historiaClinica)
    {
        $historiaClinica->load(['paraclinicos', 'diagnosticos', 'paciente.owner']);

        return view('historias_clinicas.edit', $this->formData($historiaClinica));
    }

    public function show(HistoriaClinica $historiaClinica)
    {
        $historiaClinica->load(['paraclinicos', 'diagnosticos', 'paciente.owner']);

        $prescriptions = Prescription::with(['items.product', 'professional'])
            ->where('historia_clinica_id', $historiaClinica->id)
            ->latest()
            ->get();

        $referrals = ExamReferral::where('historia_clinica_id', $historiaClinica->id)
            ->latest()
            ->get();

        return view('historias_clinicas.show', [
            'historia' => $historiaClinica,
            'prescriptions' => $prescriptions,
            'referrals' => $referrals,
        ]);
    }

    public function update(Request $request, HistoriaClinica $historiaClinica)
    {
        $validated = $this->validatedData($request);

        $historiaClinica->update($validated['fields']);
        $this->syncParaclinicos($historiaClinica, $validated['paraclinicos']);
        $this->syncDiagnosticos($historiaClinica, $validated['diagnosticos']);

        return redirect()->route('historias-clinicas.show', $historiaClinica)
            ->with('success', 'Historia clínica actualizada.');
    }

    public function destroy(HistoriaClinica $historiaClinica)
    {
        $historiaClinica->delete();

        return redirect()->route('historias-clinicas.index')
            ->with('success', 'Historia clínica eliminada.');
    }

    public function autoSave(Request $request)
    {
        $validated = $this->validatedData($request);

        $historia = HistoriaClinica::find($request->input('historia_id')) ?? new HistoriaClinica();

        $historia->fill($validated['fields']);
        $historia->estado = $historia->estado ?? 'borrador';
        $historia->save();

        $this->syncParaclinicos($historia, $validated['paraclinicos']);
        $this->syncDiagnosticos($historia, $validated['diagnosticos']);

        return response()->json([
            'id' => $historia->id,
            'saved' => true,
            'updated_at' => $historia->updated_at?->format('Y-m-d H:i:s'),
        ]);
    }

    public function pdf(HistoriaClinica $historiaClinica)
    {
        $historiaClinica->load(['paraclinicos', 'diagnosticos', 'paciente.owner']);

        $pdf = PDF::loadView('historias_clinicas.pdf', compact('historiaClinica'))
            ->setPaper('letter');

        return $pdf->stream('historia-clinica-' . $historiaClinica->id . '.pdf');
    }

    public function createRecetario(HistoriaClinica $historiaClinica)
    {
        $products = Product::orderBy('name')->get();

        return view('historias_clinicas.recetario', [
            'historia' => $historiaClinica,
            'products' => $products,
        ]);
    }

    public function storeRecetario(Request $request, HistoriaClinica $historiaClinica)
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.dose' => ['nullable', 'string'],
            'items.*.frequency' => ['nullable', 'string'],
            'items.*.duration_days' => ['nullable', 'integer', 'min:1'],
            'items.*.instructions' => ['nullable', 'string'],
            'items.*.qty_requested' => ['required', 'numeric', 'min:1'],
        ]);

        $prescription = Prescription::create([
            'historia_clinica_id' => $historiaClinica->id,
            'patient_id' => $historiaClinica->paciente_id,
            'professional_id' => Auth::id(),
            'status' => 'draft',
        ]);

        foreach ($data['items'] as $item) {
            PrescriptionItem::create($item + ['prescription_id' => $prescription->id]);
        }

        return redirect()->route('historias-clinicas.show', $historiaClinica)
            ->with('success', 'Recetario creado correctamente.');
    }

    public function facturarRecetario(Prescription $prescription, BillingService $billingService)
    {
        $prescription->load(['items.product']);
        $sale = $billingService->billPrescription($prescription);

        return redirect()->route('sales.show', $sale)->with('success', 'Recetario enviado a ventas.');
    }

    public function imprimirRecetario(Prescription $prescription)
    {
        $prescription->load(['items.product', 'historiaClinica.paciente.owner', 'professional']);

        $pdf = PDF::loadView('historias_clinicas.recetario_pdf', compact('prescription'))
            ->setPaper([0, 0, 396, 612]);

        return $pdf->stream('recetario-' . $prescription->id . '.pdf');
    }

    public function createRemision(HistoriaClinica $historiaClinica)
    {
        return view('historias_clinicas.remision', [
            'historia' => $historiaClinica,
        ]);
    }

    public function storeRemision(Request $request, HistoriaClinica $historiaClinica)
    {
        $data = $request->validate([
            'doctor_name' => ['nullable', 'string', 'max:150'],
            'tests' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        ExamReferral::create($data + [
            'historia_clinica_id' => $historiaClinica->id,
            'patient_id' => $historiaClinica->paciente_id,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('historias-clinicas.show', $historiaClinica)
            ->with('success', 'Remisión de exámenes creada.');
    }

    public function imprimirRemision(ExamReferral $examReferral)
    {
        $examReferral->load(['historiaClinica.paciente', 'author']);

        $pdf = PDF::loadView('historias_clinicas.remision_pdf', compact('examReferral'))
            ->setPaper([0, 0, 396, 612]);

        return $pdf->stream('remision-' . $examReferral->id . '.pdf');
    }

    private function formData(HistoriaClinica $historiaClinica): array
    {
        $pacientes = Paciente::orderBy('nombres')
            ->orderBy('apellidos')
            ->get();

        return [
            'historia' => $historiaClinica,
            'pacientes' => $pacientes,
        ];
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'estado' => ['nullable', 'string', 'max:40'],
            'motivo_consulta' => ['nullable', 'string'],
            'enfermedad_actual' => ['nullable', 'string'],
            'antecedentes_farmacologicos' => ['nullable', 'string'],
            'antecedentes_patologicos' => ['nullable', 'string'],
            'antecedentes_toxicologicos' => ['nullable', 'string'],
            'antecedentes_alergicos' => ['nullable', 'string'],
            'antecedentes_inmunologicos' => ['nullable', 'string'],
            'antecedentes_quirurgicos' => ['nullable', 'string'],
            'antecedentes_ginecologicos' => ['nullable', 'string'],
            'antecedentes_familiares' => ['nullable', 'string'],
            'revision_sistemas' => ['nullable', 'string'],
            'frecuencia_cardiaca' => ['nullable', 'integer', 'min:0'],
            'tension_arterial' => ['nullable', 'string', 'max:50'],
            'saturacion_oxigeno' => ['nullable', 'numeric', 'between:0,100'],
            'frecuencia_respiratoria' => ['nullable', 'integer', 'min:0'],
            'examen_cabeza_cuello' => ['nullable', 'string'],
            'examen_torax' => ['nullable', 'string'],
            'examen_corazon' => ['nullable', 'string'],
            'examen_mama' => ['nullable', 'string'],
            'examen_abdomen' => ['nullable', 'string'],
            'examen_genitales' => ['nullable', 'string'],
            'examen_neurologico' => ['nullable', 'string'],
            'examen_extremidades' => ['nullable', 'string'],
            'examen_piel' => ['nullable', 'string'],
            'analisis' => ['nullable', 'string'],
            'plan_procedimientos' => ['nullable', 'string'],
            'plan_medicamentos' => ['nullable', 'string'],
            'mipres_url' => ['nullable', 'string', 'max:255'],
            'paraclinicos_json' => ['nullable', 'string'],
            'diagnosticos_json' => ['nullable', 'string'],
        ]);

        $fields = collect($data)
            ->except(['paraclinicos_json', 'diagnosticos_json'])
            ->filter(fn ($value) => $value !== null)
            ->toArray();

        $fields['estado'] = $fields['estado'] ?? 'borrador';

        return [
            'fields' => $fields,
            'paraclinicos' => $this->prepareParaclinicos($data['paraclinicos_json'] ?? null),
            'diagnosticos' => $this->prepareDiagnosticos($data['diagnosticos_json'] ?? null),
        ];
    }

    private function prepareParaclinicos(?string $json): array
    {
        $items = $this->decodeJsonArray($json);

        return collect($items)
            ->map(fn ($item) => [
                'nombre' => trim($item['nombre'] ?? ''),
                'resultado' => $item['resultado'] ?? null,
            ])
            ->filter(fn ($item) => $item['nombre'] !== '')
            ->values()
            ->toArray();
    }

    private function prepareDiagnosticos(?string $json): array
    {
        $items = $this->decodeJsonArray($json);

        return collect($items)
            ->map(fn ($item) => [
                'codigo' => $item['codigo'] ?? null,
                'descripcion' => trim($item['descripcion'] ?? ''),
                'confirmado' => (bool) ($item['confirmado'] ?? false),
            ])
            ->filter(fn ($item) => $item['descripcion'] !== '')
            ->values()
            ->toArray();
    }

    private function decodeJsonArray(?string $json): array
    {
        if (!$json) {
            return [];
        }

        $decoded = json_decode($json, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function syncParaclinicos(HistoriaClinica $historiaClinica, array $items): void
    {
        $historiaClinica->paraclinicos()->delete();

        if (count($items)) {
            $historiaClinica->paraclinicos()->createMany($items);
        }
    }

    private function syncDiagnosticos(HistoriaClinica $historiaClinica, array $items): void
    {
        $historiaClinica->diagnosticos()->delete();

        if (count($items)) {
            $historiaClinica->diagnosticos()->createMany($items);
        }
    }
}
