<?php

namespace App\Services;

use App\Models\Dispensation;
use App\Models\Encounter;
use App\Models\HistoriaClinica;
use App\Models\HospitalStay;
use App\Models\Patient;
use App\Models\Reserva;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class TimelineService
{
    public function forPatient(Patient $patient, array $filters = []): LengthAwarePaginator|Collection
    {
        $typeFilter = $filters['type'] ?? null;
        $from = $filters['from'] ?? null;
        $to = $filters['to'] ?? null;
        $limit = $filters['limit'] ?? null;
        $perPage = $filters['perPage'] ?? 20;

        $events = collect();

        // Consultas
        if (! $typeFilter || $typeFilter === 'consulta') {
            $encounters = Encounter::where('patient_id', $patient->id)
                ->when($from, fn ($q) => $q->whereDate('occurred_at', '>=', $from))
                ->when($to, fn ($q) => $q->whereDate('occurred_at', '<=', $to))
                ->orderByDesc('occurred_at')
                ->get();

            $events = $events->merge($encounters->map(function (Encounter $encounter) {
                return [
                    'type' => 'consulta',
                    'occurred_at' => $encounter->occurred_at,
                    'title' => 'Consulta',
                    'summary' => $encounter->diagnostico ?? $encounter->motivo,
                    'url' => null,
                    'meta' => [
                        'plan' => $encounter->plan,
                        'peso' => $encounter->peso,
                        'temperatura' => $encounter->temperatura,
                        'profesional' => $encounter->professional,
                    ],
                ];
            }));
        }

        if (! $typeFilter || $typeFilter === 'historia') {
            $historias = HistoriaClinica::where('paciente_id', $patient->id)
                ->when($from, fn ($q) => $q->whereDate('created_at', '>=', $from))
                ->when($to, fn ($q) => $q->whereDate('created_at', '<=', $to))
                ->orderByDesc('created_at')
                ->get();

            $events = $events->merge($historias->map(function (HistoriaClinica $historia) {
                return [
                    'type' => 'historia',
                    'occurred_at' => $historia->updated_at ?? $historia->created_at,
                    'title' => 'Historia clínica',
                    'summary' => $historia->motivo_consulta ?: 'Consulta clínica',
                    'url' => route('historias-clinicas.show', $historia),
                    'meta' => [
                        'antecedentes' => $historia->enfermedad_actual,
                        'plan' => $historia->plan_medicamentos,
                    ],
                ];
            }));
        }

        // Reservas de peluquería/baños
        if (! $typeFilter || $typeFilter === 'banio') {
            $groomings = Reserva::where('paciente_id', $patient->id)
                ->when($from, fn ($q) => $q->whereDate('fecha', '>=', $from))
                ->when($to, fn ($q) => $q->whereDate('fecha', '<=', $to))
                ->orderByDesc('fecha')
                ->get();

            $events = $events->merge($groomings->map(function (Reserva $reserva) {
                return [
                    'type' => 'banio',
                    'occurred_at' => Carbon::parse($reserva->fecha),
                    'title' => 'Agenda de peluquería',
                    'summary' => $reserva->nota_cliente ?? 'Servicio agendado',
                    'url' => null,
                    'meta' => [
                        'estado' => $reserva->estado,
                        'modalidad' => $reserva->modalidad,
                    ],
                ];
            }));
        }

        // Hospitalización
        if (! $typeFilter || $typeFilter === 'hospital') {
            $stays = HospitalStay::where('patient_id', $patient->id)
                ->when($from, fn ($q) => $q->whereDate('admitted_at', '>=', $from))
                ->when($to, fn ($q) => $q->whereDate('admitted_at', '<=', $to))
                ->orderByDesc('admitted_at')
                ->get();

            $events = $events->merge($stays->map(function (HospitalStay $stay) {
                return [
                    'type' => 'hospital',
                    'occurred_at' => $stay->admitted_at,
                    'title' => 'Hospitalización',
                    'summary' => $stay->diagnosis,
                    'url' => null,
                    'meta' => [
                        'estado' => $stay->status,
                        'plan' => $stay->plan,
                    ],
                ];
            }));
        }

        // Dispensaciones
        if (! $typeFilter || $typeFilter === 'dispensacion') {
            $dispensations = Dispensation::whereHas('prescription', function ($query) use ($patient) {
                $query->where('patient_id', $patient->id);
            })
                ->with('prescription')
                ->when($from, fn ($q) => $q->whereDate('dispensed_at', '>=', $from))
                ->when($to, fn ($q) => $q->whereDate('dispensed_at', '<=', $to))
                ->orderByDesc('dispensed_at')
                ->get();

            $events = $events->merge($dispensations->map(function (Dispensation $dispensation) {
                return [
                    'type' => 'dispensacion',
                    'occurred_at' => $dispensation->dispensed_at ?? $dispensation->created_at,
                    'title' => 'Dispensación de fórmula',
                    'summary' => 'Fórmula entregada',
                    'url' => null,
                    'meta' => [
                        'estado' => $dispensation->status,
                    ],
                ];
            }));
        }

        // Ventas
        if (! $typeFilter || $typeFilter === 'venta') {
            $sales = Sale::where('patient_id', $patient->id)
                ->when($from, fn ($q) => $q->whereDate('created_at', '>=', $from))
                ->when($to, fn ($q) => $q->whereDate('created_at', '<=', $to))
                ->orderByDesc('created_at')
                ->get();

            $events = $events->merge($sales->map(function (Sale $sale) {
                return [
                    'type' => 'venta',
                    'occurred_at' => $sale->created_at,
                    'title' => 'Venta / Cobro',
                    'summary' => 'Ticket #' . $sale->id,
                    'url' => null,
                    'meta' => [
                        'total' => $sale->total,
                        'estado' => $sale->status,
                    ],
                ];
            }));
        }

        $sorted = $events->sortByDesc('occurred_at')->values();

        if ($limit) {
            return $sorted->take($limit);
        }

        $page = Paginator::resolveCurrentPage();
        $items = $sorted->forPage($page, $perPage)->values();

        return new LengthAwarePaginator(
            $items,
            $sorted->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath(), 'query' => array_filter($filters)]
        );
    }
}
