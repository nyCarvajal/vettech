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
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

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

        $prescriptions = Prescription::with(['items.product'])
            ->where('historia_clinica_id', $historiaClinica->id)
            ->latest()
            ->get();

        $referrals = ExamReferral::where('historia_clinica_id', $historiaClinica->id)
            ->latest()
            ->get();

        $attachments = $historiaClinica->adjuntos;

        return view('historias_clinicas.show', [
            'historia' => $historiaClinica,
            'prescriptions' => $prescriptions,
            'referrals' => $referrals,
            'attachments' => $attachments,
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
        $historiaClinica->load([
            'paraclinicos',
            'diagnosticos',
            'paciente.owner',
            'paciente.species',
            'paciente.breed',
            'adjuntos' => function ($query) {
                $query->whereIn('file_type', ['image', 'pdf']);
            },
        ]);

        $pdf = Pdf::loadView('historias_clinicas.pdf', compact('historiaClinica'))
            ->setPaper('letter');

        return $pdf->stream('historia-clinica-' . $historiaClinica->id . '.pdf');
    }

    public function createRecetario(HistoriaClinica $historiaClinica)
    {
        $products = Product::orderBy('name')->get();

        return view('historias_clinicas.recetario', [
            'historia' => $historiaClinica,
            'products' => $products,
            'pacientes' => collect(),
        ]);
    }

    public function createRecetarioQuick()
    {
        $products = Product::orderBy('name')->get();
        $pacientes = Paciente::with(['owner', 'breed'])
            ->orderBy('nombres')
            ->orderBy('apellidos')
            ->get();

        return view('historias_clinicas.recetario', [
            'historia' => null,
            'products' => $products,
            'pacientes' => $pacientes,
        ]);
    }

    public function storeRecetario(Request $request, HistoriaClinica $historiaClinica)
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.is_manual' => ['boolean'],
            'items.*.product_id' => ['required_without:items.*.manual_name', 'nullable', 'exists:products,id'],
            'items.*.manual_name' => ['required_without:items.*.product_id', 'nullable', 'string', 'max:255'],
            'items.*.dose' => ['nullable', 'string'],
            'items.*.frequency' => ['nullable', 'string'],
            'items.*.duration_days' => ['nullable', 'integer', 'min:1'],
            'items.*.instructions' => ['nullable', 'string'],
            'items.*.qty_requested' => ['required', 'numeric', 'min:1'],
        ]);

        $this->persistRecetario($historiaClinica, $data['items']);

        return redirect()->route('historias-clinicas.show', $historiaClinica)
            ->with('success', 'Recetario creado correctamente.');
    }

    public function storeRecetarioQuick(Request $request)
    {
        $data = $request->validate([
            'patient_id' => ['required', 'exists:pacientes,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.is_manual' => ['boolean'],
            'items.*.product_id' => ['required_without:items.*.manual_name', 'nullable', 'exists:products,id'],
            'items.*.manual_name' => ['required_without:items.*.product_id', 'nullable', 'string', 'max:255'],
            'items.*.dose' => ['nullable', 'string'],
            'items.*.frequency' => ['nullable', 'string'],
            'items.*.duration_days' => ['nullable', 'integer', 'min:1'],
            'items.*.instructions' => ['nullable', 'string'],
            'items.*.qty_requested' => ['required', 'numeric', 'min:1'],
        ]);

        $historiaClinica = HistoriaClinica::where('paciente_id', $data['patient_id'])
            ->latest()
            ->first();

        if (! $historiaClinica) {
            $historiaClinica = HistoriaClinica::create([
                'paciente_id' => $data['patient_id'],
                'estado' => 'borrador',
            ]);
        }

        $this->persistRecetario($historiaClinica, $data['items']);

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
        try {
            $prescription->load([
                'items.product',
                'professional',
                'historiaClinica.paciente.owner',
                'historiaClinica.paciente.species',
                'historiaClinica.paciente.breed',
            ]);

            $clinica = optional(Auth::user())->clinica;

            $pdf = Pdf::loadView('historias_clinicas.recetario_pdf', compact('prescription', 'clinica'))
                ->setPaper([0, 0, 396, 612]);

            return $pdf->stream('recetario-' . $prescription->id . '.pdf');
        } catch (\Throwable $exception) {
            $context = [
                'prescription_id' => $prescription->id,
                'user_id' => Auth::id(),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ];

            Log::channel('single')->error('Error generando recetario PDF', $context);

            @file_put_contents(
                storage_path('logs/laravel.log'),
                '[' . now()->toDateTimeString() . "] recetario_pdf_error " . json_encode($context) . PHP_EOL,
                FILE_APPEND
            );

            throw $exception;
        }
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

        $data['doctor_name'] = optional(Auth::user())->name ?: ($data['doctor_name'] ?? null);

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

        $pdf = Pdf::loadView('historias_clinicas.remision_pdf', compact('examReferral'))
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

    private function persistRecetario(HistoriaClinica $historiaClinica, array $items): void
    {
        $prescription = Prescription::create([
            'historia_clinica_id' => $historiaClinica->id,
            'patient_id' => $historiaClinica->paciente_id,
            'professional_id' => Auth::id(),
            'status' => 'draft',
        ]);

        foreach ($items as $item) {
            $isManual = (bool) ($item['is_manual'] ?? false);

            PrescriptionItem::create([
                'prescription_id' => $prescription->id,
                'product_id' => $isManual ? null : ($item['product_id'] ?? null),
                'manual_name' => $item['manual_name'] ?? null,
                'is_manual' => $isManual,
                'billable' => ! $isManual,
                'dose' => $item['dose'] ?? '',
                'frequency' => $item['frequency'] ?? '',
                'duration_days' => $item['duration_days'] ?? 0,
                'instructions' => $item['instructions'] ?? null,
                'qty_requested' => $item['qty_requested'],
            ]);
        }
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
            'temperatura' => ['nullable', 'numeric', 'between:20,50'],
            'peso' => ['nullable', 'numeric', 'between:0,200000'],
            'trc' => ['nullable', 'string', 'max:20'],
            'mucosas' => ['nullable', 'string', 'max:100'],
            'hidratacion' => ['nullable', 'string', 'max:100'],
            'condicion_corporal' => ['nullable', 'string', 'max:100'],
            'frecuencia_cardiaca' => ['nullable', 'integer', 'min:0'],
            'tension_arterial' => ['nullable', 'string', 'max:50'],
            'saturacion_oxigeno' => ['nullable', 'numeric', 'between:0,100'],
            'frecuencia_respiratoria' => ['nullable', 'integer', 'min:0'],
            'estado_mental' => ['nullable', 'string'],
            'postura' => ['nullable', 'string'],
            'marcha' => ['nullable', 'string'],
            'dolor' => ['nullable', 'string'],
            'examen_cabeza_cuello' => ['nullable', 'string'],
            'examen_ojos' => ['nullable', 'string'],
            'examen_oidos' => ['nullable', 'string'],
            'examen_boca' => ['nullable', 'string'],
            'examen_ganglios' => ['nullable', 'string'],
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
