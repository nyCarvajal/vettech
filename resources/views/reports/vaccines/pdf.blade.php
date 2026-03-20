<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #0f172a; font-size: 11px; }
        .header { border-bottom: 2px solid #10b981; padding-bottom: 10px; margin-bottom: 16px; }
        .muted { color: #64748b; }
        .card { border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px; margin-bottom: 10px; }
        .title { font-size: 14px; font-weight: bold; margin-bottom: 6px; }
        .grid { width: 100%; }
        .grid td { vertical-align: top; padding: 2px 8px 2px 0; }
        .pill { display: inline-block; padding: 2px 8px; border-radius: 999px; background: #dcfce7; font-size: 10px; }
        .pill-rabies { background: #ffe4e6; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Informe de vacunas</div>
        <div class="muted">Rango: {{ $filters->rangeLabel() }} · Generado: {{ now()->format('d/m/Y H:i') }}</div>
        <div class="muted">Filtros: Rabia {{ $extraFilters['rabies'] === 'all' ? 'todas' : ($extraFilters['rabies'] === 'yes' ? 'sí' : 'no') }}, origen {{ $extraFilters['source'] === 'all' ? 'todos' : ($extraFilters['source'] === 'inventory' ? 'inventario' : 'manual') }}.</div>
    </div>

    @forelse($records as $row)
        <div class="card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                <div class="title">{{ $row->vaccine_name }}</div>
                <div>
                    <span class="pill">{{ $row->status_label }}</span>
                    @if($row->contains_rabies)
                        <span class="pill pill-rabies">Rabia</span>
                    @endif
                </div>
            </div>

            <table class="grid">
                <tr>
                    <td width="33%"><strong>Fecha:</strong> {{ $row->applied_at ? \Carbon\Carbon::parse($row->applied_at)->format('d/m/Y') : 'N/D' }}</td>
                    <td width="33%"><strong>Origen:</strong> {{ $row->source_label }}</td>
                    <td width="33%"><strong>Lote:</strong> {{ $row->batch_lot ?: 'N/D' }}</td>
                </tr>
                <tr>
                    <td><strong>Producto:</strong> {{ $row->inventory_item_name ?: $row->manual_item_name ?: 'N/D' }}</td>
                    <td><strong>Dosis:</strong> {{ $row->dose ?: 'N/D' }}</td>
                    <td><strong>Veterinario:</strong> {{ $row->vet_name ?: 'N/D' }}</td>
                </tr>
                <tr>
                    <td><strong>Próxima dosis:</strong> {{ $row->next_due_at ? \Carbon\Carbon::parse($row->next_due_at)->format('d/m/Y') : 'N/D' }}</td>
                    <td><strong>Vence:</strong> {{ $row->expires_at ? \Carbon\Carbon::parse($row->expires_at)->format('d/m/Y') : 'N/D' }}</td>
                    <td><strong>Notas:</strong> {{ $row->notes ?: 'N/D' }}</td>
                </tr>
            </table>

            <hr style="border:none;border-top:1px solid #e2e8f0;margin:8px 0;">
            <table class="grid">
                <tr>
                    <td width="50%"><strong>Tutor principal:</strong> {{ $row->owner_name ?: 'N/D' }}</td>
                    <td width="50%"><strong>Documento:</strong> {{ trim(($row->owner_document_type ?: '') . ' ' . ($row->owner_document ?: '')) ?: 'N/D' }}</td>
                </tr>
                <tr>
                    <td><strong>Teléfono:</strong> {{ $row->owner_phone ?: 'N/D' }}</td>
                    <td><strong>WhatsApp:</strong> {{ $row->owner_whatsapp ?: 'N/D' }}</td>
                </tr>
                <tr>
                    <td><strong>Email:</strong> {{ $row->owner_email ?: 'N/D' }}</td>
                    <td><strong>Ciudad:</strong> {{ $row->owner_city ?: 'N/D' }}</td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Dirección / notas:</strong> {{ trim(($row->owner_address ?: 'N/D') . ' · ' . ($row->owner_notes ?: 'Sin notas')) }}</td>
                </tr>
            </table>

            <hr style="border:none;border-top:1px solid #e2e8f0;margin:8px 0;">
            <table class="grid">
                <tr>
                    <td width="50%"><strong>Mascota:</strong> {{ $row->patient_name ?: 'N/D' }} (#{{ $row->patient_id }})</td>
                    <td width="50%"><strong>Especie / raza:</strong> {{ trim(($row->species_name ?: 'N/D') . ' / ' . ($row->breed_name ?: 'N/D')) }}</td>
                </tr>
                <tr>
                    <td><strong>Sexo:</strong> {{ $row->patient_sex ?: 'N/D' }}</td>
                    <td><strong>Edad / nacimiento:</strong> {{ ($row->patient_age ?: 'N/D') . ' · ' . ($row->patient_birthdate ? \Carbon\Carbon::parse($row->patient_birthdate)->format('d/m/Y') : 'N/D') }}</td>
                </tr>
                <tr>
                    <td><strong>Color:</strong> {{ $row->patient_color ?: 'N/D' }}</td>
                    <td><strong>Microchip:</strong> {{ $row->patient_microchip ?: 'N/D' }}</td>
                </tr>
                <tr>
                    <td><strong>Peso / temperamento:</strong> {{ ($row->patient_weight ?: 'N/D') . ' · ' . ($row->patient_temperament ?: 'N/D') }}</td>
                    <td><strong>Alergias:</strong> {{ $row->patient_allergies ?: 'N/D' }}</td>
                </tr>
                <tr>
                    <td><strong>Contacto mascota:</strong> {{ trim(($row->patient_whatsapp ?: 'N/D') . ' · ' . ($row->patient_email ?: 'N/D')) }}</td>
                    <td><strong>Ubicación:</strong> {{ trim(($row->patient_address ?: 'N/D') . ' · ' . ($row->patient_city ?: 'N/D')) }}</td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Observaciones mascota:</strong> {{ $row->patient_notes ?: 'N/D' }}</td>
                </tr>
            </table>
        </div>
    @empty
        <p class="muted">No hay vacunas registradas para los filtros seleccionados.</p>
    @endforelse
</body>
</html>
