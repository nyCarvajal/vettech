<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; color:#0f172a; }
        .header { display:flex; justify-content:space-between; align-items:center; padding:12px 18px; background:linear-gradient(135deg,#ede9fe,#d1fae5); border-radius:12px; }
        .section { margin-top:18px; }
        .card { border:1px solid #e5e7eb; border-radius:12px; padding:12px; margin-bottom:8px; }
        .badge { padding:4px 10px; border-radius:999px; font-size:11px; font-weight:700; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h2 style="margin:0;">Carnet de {{ $patient->display_name }}</h2>
            <p style="margin:4px 0 0;">Tutor: {{ optional($patient->owner)->name }} · {{ optional($patient->species)->name }}</p>
        </div>
        <div style="text-align:right;">
            <div style="font-size:26px;">💉🦠🛡️</div>
            <p style="margin:0;font-size:12px;">Emitido: {{ now()->format('d/m/Y') }}</p>
        </div>
    </div>

    <div class="section">
        <h3>Vacunas</h3>
        @forelse($immunizations as $vaccine)
            <div class="card">
                <div style="display:flex; justify-content:space-between;">
                    <div>
                        <strong>{{ $vaccine->vaccine_name }}</strong> <span class="badge" style="background:#d9f99d;">{{ ucfirst($vaccine->status) }}</span>
                        @if($vaccine->contains_rabies)
                            <span class="badge" style="background:#fecdd3;">Rabia</span>
                        @endif
                        <p style="margin:4px 0 0; font-size:12px;">Producto: {{ $vaccine->item->nombre ?? $vaccine->item_manual }}</p>
                        <p style="margin:0; font-size:12px;">Lote: {{ $vaccine->batch_lot }} · Dosis: {{ $vaccine->dose ?: 'N/D' }}</p>
                        @if($vaccine->next_due_at)
                            <p style="margin:0; font-size:12px;">Próxima: {{ optional($vaccine->next_due_at)->format('d/m/Y') }}</p>
                        @endif
                    </div>
                    <div style="text-align:right; font-size:12px;">{{ optional($vaccine->applied_at)->format('d/m/Y') }}</div>
                </div>
            </div>
        @empty
            <p style="color:#6b7280;">Sin vacunas registradas.</p>
        @endforelse
    </div>

    <div class="section">
        <h3>Desparasitación interna</h3>
        @forelse($internalDewormings as $item)
            <div class="card">
                <strong>{{ $item->item->nombre ?? $item->item_manual }}</strong>
                <p style="margin:4px 0 0; font-size:12px;">Dosis: {{ $item->dose ?: 'N/D' }} · Ruta: {{ $item->route ?: 'N/D' }}</p>
                @if($item->next_due_at)
                    <p style="margin:0; font-size:12px;">Próxima: {{ optional($item->next_due_at)->format('d/m/Y') }}</p>
                @endif
            </div>
        @empty
            <p style="color:#6b7280;">Sin registros internos.</p>
        @endforelse
    </div>

    <div class="section">
        <h3>Desparasitación externa</h3>
        @forelse($externalDewormings as $item)
            <div class="card">
                <strong>{{ $item->item->nombre ?? $item->item_manual }}</strong>
                <p style="margin:4px 0 0; font-size:12px;">Dosis: {{ $item->dose ?: 'N/D' }} · Ruta: {{ $item->route ?: 'N/D' }}</p>
                @if($item->duration_days)
                    <p style="margin:0; font-size:12px;">Duración: {{ $item->duration_days }} días</p>
                @endif
                @if($item->next_due_at)
                    <p style="margin:0; font-size:12px;">Próxima: {{ optional($item->next_due_at)->format('d/m/Y') }}</p>
                @endif
            </div>
        @empty
            <p style="color:#6b7280;">Sin registros externos.</p>
        @endforelse
    </div>
</body>
</html>
