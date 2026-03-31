<?php

namespace App\Reports;

use Carbon\Carbon;

class VaccinesReportRepository extends ReportRepository
{
    public function summary(ReportFilters $filters, array $options = []): array
    {
        $baseQuery = $this->baseQuery($filters, $options);

        $totals = (clone $baseQuery)
            ->selectRaw('COUNT(*) as total_vaccines')
            ->selectRaw('SUM(CASE WHEN patient_immunizations.contains_rabies = 1 THEN 1 ELSE 0 END) as rabies_count')
            ->selectRaw('SUM(CASE WHEN patient_immunizations.item_id IS NOT NULL THEN 1 ELSE 0 END) as inventory_count')
            ->selectRaw('SUM(CASE WHEN patient_immunizations.item_id IS NULL THEN 1 ELSE 0 END) as manual_count')
            ->first();

        $series = (clone $baseQuery)
            ->selectRaw($this->dateGroupExpression('patient_immunizations.applied_at', $filters->granularity) . ' as label')
            ->selectRaw('COUNT(*) as value')
            ->groupBy('label')
            ->orderBy('label')
            ->get();

        $statusBreakdown = (clone $baseQuery)
            ->selectRaw('patient_immunizations.status as label')
            ->selectRaw('COUNT(*) as value')
            ->groupBy('patient_immunizations.status')
            ->orderByDesc('value')
            ->get();

        $table = $this->detailedRowsQuery($filters, $options)
            ->orderByDesc('patient_immunizations.applied_at')
            ->orderByDesc('patient_immunizations.id');

        return [
            'kpis' => [
                'total_vaccines' => (int) ($totals->total_vaccines ?? 0),
                'rabies_count' => (int) ($totals->rabies_count ?? 0),
                'inventory_count' => (int) ($totals->inventory_count ?? 0),
                'manual_count' => (int) ($totals->manual_count ?? 0),
            ],
            'series' => $series,
            'status_breakdown' => $statusBreakdown,
            'table' => $table,
        ];
    }

    public function exportData(ReportFilters $filters, array $options = []): array
    {
        $rows = $this->detailedRowsQuery($filters, $options)
            ->orderBy('patient_immunizations.applied_at')
            ->orderBy('patient_immunizations.id')
            ->get()
            ->map(function ($row) {
                return [
                    'fecha_aplicacion' => $row->applied_at ? \Carbon\Carbon::parse($row->applied_at)->format('Y-m-d') : null,
                    'vacuna' => $row->vaccine_name,
                    'contiene_rabia' => $row->contains_rabies ? 'Sí' : 'No',
                    'origen_registro' => $row->source_label,
                    'producto_inventario' => $row->inventory_item_name,
                    'producto_manual' => $row->manual_item_name,
                    'lote' => $row->batch_lot,
                    'dosis' => $row->dose,
                    'proxima_dosis' => $row->next_due_at ? \Carbon\Carbon::parse($row->next_due_at)->format('Y-m-d') : null,
                    'vence' => $row->expires_at ? \Carbon\Carbon::parse($row->expires_at)->format('Y-m-d') : null,
                    'estado' => $row->status_label,
                    'veterinario' => $row->vet_name,
                    'notas' => $row->notes,
                    'mascota_id' => $row->patient_id,
                    'mascota' => $row->patient_name,
                    'especie' => $row->species_name,
                    'raza' => $row->breed_name,
                    'sexo' => $row->patient_sex,
                    'fecha_nacimiento' => $row->patient_birthdate ? \Carbon\Carbon::parse($row->patient_birthdate)->format('Y-m-d') : null,
                    'edad' => $row->patient_age,
                    'color' => $row->patient_color,
                    'microchip' => $row->patient_microchip,
                    'peso' => $row->patient_weight,
                    'temperamento' => $row->patient_temperament,
                    'alergias' => $row->patient_allergies,
                    'observaciones_mascota' => $row->patient_notes,
                    'email_mascota' => $row->patient_email,
                    'whatsapp_mascota' => $row->patient_whatsapp,
                    'direccion_mascota' => $row->patient_address,
                    'ciudad_mascota' => $row->patient_city,
                    'tutor_principal' => $row->owner_name,
                    'tipo_documento_tutor' => $row->owner_document_type,
                    'documento_tutor' => $row->owner_document,
                    'telefono_tutor' => $row->owner_phone,
                    'whatsapp_tutor' => $row->owner_whatsapp,
                    'email_tutor' => $row->owner_email,
                    'direccion_tutor' => $row->owner_address,
                    'ciudad_tutor' => $row->owner_city,
                    'notas_tutor' => $row->owner_notes,
                ];
            })
            ->all();

        return [
            'headers' => [
                'Fecha aplicación', 'Vacuna', 'Contiene rabia', 'Origen registro', 'Producto inventario', 'Producto manual', 'Lote', 'Dosis',
                'Próxima dosis', 'Vence', 'Estado', 'Veterinario', 'Notas', 'Mascota ID', 'Mascota', 'Especie', 'Raza', 'Sexo',
                'Fecha nacimiento', 'Edad', 'Color', 'Microchip', 'Peso', 'Temperamento', 'Alergias', 'Observaciones mascota',
                'Email mascota', 'WhatsApp mascota', 'Dirección mascota', 'Ciudad mascota', 'Tutor principal', 'Tipo documento tutor',
                'Documento tutor', 'Teléfono tutor', 'WhatsApp tutor', 'Email tutor', 'Dirección tutor', 'Ciudad tutor', 'Notas tutor',
            ],
            'rows' => $rows,
        ];
    }

    public function recordsForPdf(ReportFilters $filters, array $options = [])
    {
        return $this->detailedRowsQuery($filters, $options)
            ->orderByDesc('patient_immunizations.applied_at')
            ->orderByDesc('patient_immunizations.id')
            ->get();
    }

    private function baseQuery(ReportFilters $filters, array $options = [])
    {
        $query = $this->connection()->table('patient_immunizations')
            ->join('pacientes', 'pacientes.id', '=', 'patient_immunizations.paciente_id')
            ->leftJoin('owners', 'owners.id', '=', 'pacientes.owner_id');

        $this->applyDateRange($query, 'patient_immunizations.applied_at', $filters);
        $this->applyTenant($query, 'patient_immunizations', $filters);
        $this->applyOptionalFilters($query, $filters, 'patient_immunizations.vet_user_id', 'pacientes.owner_id');
        $this->applyExtraFilters($query, $options);

        return $query;
    }

    private function detailedRowsQuery(ReportFilters $filters, array $options = [])
    {
        $hasUsuariosTable = \Illuminate\Support\Facades\Schema::connection($this->connectionName())->hasTable('usuarios');

        $query = $this->connection()->table('patient_immunizations')
            ->join('pacientes', 'pacientes.id', '=', 'patient_immunizations.paciente_id')
            ->leftJoin('owners', 'owners.id', '=', 'pacientes.owner_id')
            ->leftJoin('species', 'species.id', '=', 'pacientes.species_id')
            ->leftJoin('breeds', 'breeds.id', '=', 'pacientes.breed_id')
            ->leftJoin('items', 'items.id', '=', 'patient_immunizations.item_id');

        if ($hasUsuariosTable) {
            $query->leftJoin('usuarios', 'usuarios.id', '=', 'patient_immunizations.vet_user_id');
        }

        $this->applyDateRange($query, 'patient_immunizations.applied_at', $filters);
        $this->applyTenant($query, 'patient_immunizations', $filters);
        $this->applyOptionalFilters($query, $filters, 'patient_immunizations.vet_user_id', 'pacientes.owner_id');
        $this->applyExtraFilters($query, $options);

        $itemNameColumn = $this->resolveItemNameColumn();
        $speciesNameColumn = $this->tableHasColumn('species', 'name') ? 'species.name' : 'species.nombre';
        $breedNameColumn = $this->tableHasColumn('breeds', 'name') ? 'breeds.name' : 'breeds.nombre';
        $patientEmailColumn = $this->tableHasColumn('pacientes', 'email')
            ? 'pacientes.email'
            : ($this->tableHasColumn('pacientes', 'correo') ? 'pacientes.correo' : 'owners.email');
        $patientWhatsappColumn = $this->tableHasColumn('pacientes', 'whatsapp') ? 'pacientes.whatsapp' : 'owners.whatsapp';
        $patientAddressColumn = $this->tableHasColumn('pacientes', 'direccion') ? 'pacientes.direccion' : 'owners.address';
        $patientCityColumn = $this->tableHasColumn('pacientes', 'ciudad')
            ? 'pacientes.ciudad'
            : ($this->tableHasColumn('pacientes', 'municipio') ? 'pacientes.municipio' : 'owners.city');
        $vetNameExpression = $hasUsuariosTable
            ? "TRIM(CONCAT(COALESCE(usuarios.nombre, ''), ' ', COALESCE(usuarios.apellidos, '')))"
            : "''";
        $driver = $this->connection()->getDriverName();
        $patientAgeExpression = $driver === 'sqlite'
            ? "CASE
                WHEN pacientes.age_value IS NOT NULL THEN pacientes.age_value || ' ' || CASE WHEN pacientes.age_unit = 'months' THEN 'mes(es)' ELSE 'año(s)' END
                WHEN pacientes.fecha_nacimiento IS NOT NULL THEN CAST((julianday('now') - julianday(pacientes.fecha_nacimiento)) / 365.25 AS INTEGER)
                ELSE NULL
            END"
            : "CASE
                WHEN pacientes.age_value IS NOT NULL THEN CONCAT(pacientes.age_value, ' ', CASE WHEN pacientes.age_unit = 'months' THEN 'mes(es)' ELSE 'año(s)' END)
                WHEN pacientes.fecha_nacimiento IS NOT NULL THEN TIMESTAMPDIFF(YEAR, pacientes.fecha_nacimiento, CURDATE())
                ELSE NULL
            END";

        return $query->selectRaw('patient_immunizations.id')
            ->selectRaw('patient_immunizations.applied_at')
            ->selectRaw('patient_immunizations.vaccine_name')
            ->selectRaw('patient_immunizations.contains_rabies')
            ->selectRaw('patient_immunizations.item_manual as manual_item_name')
            ->selectRaw("{$itemNameColumn} as inventory_item_name")
            ->selectRaw('patient_immunizations.batch_lot')
            ->selectRaw('patient_immunizations.dose')
            ->selectRaw('patient_immunizations.next_due_at')
            ->selectRaw('patient_immunizations.expires_at')
            ->selectRaw('patient_immunizations.status')
            ->selectRaw("CASE patient_immunizations.status
                WHEN 'applied' THEN 'Aplicada'
                WHEN 'scheduled' THEN 'Programada'
                WHEN 'overdue' THEN 'Vencida'
                ELSE patient_immunizations.status
            END as status_label")
            ->selectRaw("CASE WHEN patient_immunizations.item_id IS NULL THEN 'Manual' ELSE 'Inventario' END as source_label")
            ->selectRaw('patient_immunizations.notes')
            ->selectRaw("{$vetNameExpression} as vet_name")
            ->selectRaw('pacientes.id as patient_id')
            ->selectRaw("TRIM(CONCAT(COALESCE(pacientes.nombres, ''), ' ', COALESCE(pacientes.apellidos, ''))) as patient_name")
            ->selectRaw("{$speciesNameColumn} as species_name")
            ->selectRaw("{$breedNameColumn} as breed_name")
            ->selectRaw('pacientes.sexo as patient_sex')
            ->selectRaw('pacientes.fecha_nacimiento as patient_birthdate')
            ->selectRaw('pacientes.color as patient_color')
            ->selectRaw('pacientes.microchip as patient_microchip')
            ->selectRaw('pacientes.peso_actual as patient_weight')
            ->selectRaw('pacientes.temperamento as patient_temperament')
            ->selectRaw('pacientes.alergias as patient_allergies')
            ->selectRaw('pacientes.observaciones as patient_notes')
            ->selectRaw("{$patientEmailColumn} as patient_email")
            ->selectRaw("{$patientWhatsappColumn} as patient_whatsapp")
            ->selectRaw("{$patientAddressColumn} as patient_address")
            ->selectRaw("{$patientCityColumn} as patient_city")
            ->selectRaw($patientAgeExpression . ' as patient_age')
            ->selectRaw('owners.name as owner_name')
            ->selectRaw('owners.document_type as owner_document_type')
            ->selectRaw('owners.document as owner_document')
            ->selectRaw('owners.phone as owner_phone')
            ->selectRaw('owners.whatsapp as owner_whatsapp')
            ->selectRaw('owners.email as owner_email')
            ->selectRaw('owners.address as owner_address')
            ->selectRaw('owners.city as owner_city')
            ->selectRaw('owners.notes as owner_notes');
    }

    private function applyExtraFilters($query, array $options): void
    {
        $rabies = $options['rabies'] ?? 'all';
        if ($rabies === 'yes') {
            $query->where('patient_immunizations.contains_rabies', true);
        } elseif ($rabies === 'no') {
            $query->where('patient_immunizations.contains_rabies', false);
        }

        $source = $options['source'] ?? 'all';
        if ($source === 'inventory') {
            $query->whereNotNull('patient_immunizations.item_id');
        } elseif ($source === 'manual') {
            $query->whereNull('patient_immunizations.item_id');
        }

        $search = trim((string) ($options['q'] ?? ''));
        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('patient_immunizations.vaccine_name', 'like', "%{$search}%")
                    ->orWhere('patient_immunizations.item_manual', 'like', "%{$search}%")
                    ->orWhere('patient_immunizations.batch_lot', 'like', "%{$search}%")
                    ->orWhere('pacientes.nombres', 'like', "%{$search}%")
                    ->orWhere('pacientes.apellidos', 'like', "%{$search}%")
                    ->orWhere('owners.name', 'like', "%{$search}%")
                    ->orWhere('owners.document', 'like', "%{$search}%");
            });
        }
    }
}
