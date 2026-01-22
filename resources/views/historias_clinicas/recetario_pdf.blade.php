<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 20px 24px; }
        :root {
            --purple: #5e4cfa;
            --mint: #44d4b7;
            --mint-soft: #e8fbf5;
            --ink: #1a1a1a;
            --muted: #5f6472;
            --line: #e3e6ef;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: var(--ink);
            font-size: 12px;
            line-height: 1.45;
            background: #fff;
        }
        .card {
            border: 1px solid var(--line);
            border-radius: 14px;
            overflow: hidden;
        }
        .header {
            display: flex;
            align-items: center;
            padding: 16px 18px;
            background: rgba(94, 76, 250, 0.96);
            color: #fff;
        }
        .brand-mark {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            background: #fff;
            display: grid;
            place-items: center;
            margin-right: 14px;
            overflow: hidden;
        }
        .brand-mark img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .brand-mark span {
            font-size: 26px;
            color: var(--purple);
        }
        .header h1 { margin: 0; font-size: 18px; letter-spacing: 0.2px; }
        .header p { margin: 2px 0 0; color: rgba(255,255,255,0.82); font-size: 11px; }
        .meta-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            padding: 14px 18px;
            background: #f9fafc;
            border-bottom: 1px solid var(--line);
        }
        .meta-card {
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 10px 12px;
            background: #fff;
        }
        .meta-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.6px; color: var(--muted); margin-bottom: 4px; }
        .meta-value { font-weight: 600; font-size: 12px; color: var(--ink); }
        .section {
            padding: 14px 18px 6px;
        }
        .section-title {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--muted);
            margin: 0 0 8px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 10px;
            background: var(--mint-soft);
            border: 1px solid #c5f1e3;
            border-radius: 10px;
            margin-bottom: 10px;
        }
        .pill strong { color: #147f68; font-size: 12px; }
        .pill small { color: var(--muted); font-size: 11px; }
        .grid-two {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }
        .info-card {
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 10px 12px;
            background: #fff;
        }
        .info-card h3 { margin: 0 0 6px; font-size: 13px; color: var(--purple); }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td { padding: 4px 0; vertical-align: top; font-size: 12px; }
        .info-table .label { color: var(--muted); width: 38%; }
        .info-table .value { font-weight: 600; color: var(--ink); }
        .rx-block {
            margin-top: 8px;
            border: 1px solid var(--line);
            border-radius: 10px;
            overflow: hidden;
        }
        .rx-head {
            background: linear-gradient(90deg, rgba(94, 76, 250, 0.1), rgba(68, 212, 183, 0.16));
            padding: 10px 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            color: var(--ink);
        }
        .rx-head span { color: var(--purple); font-size: 15px; }
        .rx-table { width: 100%; border-collapse: collapse; }
        .rx-table th { text-align: left; font-size: 11px; letter-spacing: 0.4px; text-transform: uppercase; color: var(--muted); padding: 8px 10px; background: #f9fafc; }
        .rx-table td { padding: 8px 10px; border-top: 1px solid var(--line); font-size: 12px; vertical-align: top; }
        .rx-table .name { font-weight: 700; color: var(--ink); }
        .rx-table .muted { color: var(--muted); font-weight: 500; }
        .footer {
            margin-top: 12px;
            padding: 12px 18px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(110deg, rgba(94, 76, 250, 0.08), rgba(68, 212, 183, 0.08));
            border-top: 1px solid var(--line);
        }
        .signature { margin-top: 28px; text-align: center; }
        .signature-line { height: 1px; background: var(--line); margin: 18px 0 8px; }
        .tiny { font-size: 10px; color: var(--muted); }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            @php
                $clinicLogoPath = $clinica->logo_path
                    ? public_path('storage/' . $clinica->logo_path)
                    : public_path('images/logo-dark.png');
            @endphp
            <div class="brand-mark">
                @if ($clinicLogoPath && file_exists($clinicLogoPath))
                    <img src="{{ $clinicLogoPath }}" alt="Logo {{ $clinica->name ?? $clinica->nombre ?? config('app.name') }}">
                @else
                    <span>❤</span>
                @endif
            </div>
            <div>
                <h1>Receta Mádica</h1>
                <p>Rx #{{ $prescription->id }} · {{ $prescription->created_at?->format('d/m/Y') }}</p>
            </div>
           
        </div>

        <div class="section">
            <div class="grid-two">
                <div class="info-card">
                    <h3>Datos del tutor</h3>
                    <table class="info-table">
                        <tr>
                            <td class="label">Documento</td>
                            <td class="value">{{ optional(optional($prescription->historiaClinica?->paciente)->owner)->document_type ?? 'N/D' }}</td>
                            <td class="value">{{ optional(optional($prescription->historiaClinica?->paciente)->owner)->document ?? 'N/D' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Nombre</td>
                            <td class="value">{{ optional(optional($prescription->historiaClinica?->paciente)->owner)->name ?? 'N/D' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Dirección</td>
                            <td class="value">{{ optional(optional($prescription->historiaClinica?->paciente)->owner)->address ?? 'N/D' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Whatsapp</td>
                            <td class="value">{{ optional(optional($prescription->historiaClinica?->paciente)->owner)->whatsapp ?? 'N/D' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="info-card">
                    <h3>Datos del paciente</h3>
                    <table class="info-table">
                        <tr>
                            <td class="label">Nombre</td>
                            <td class="value">{{ trim(optional($prescription->historiaClinica?->paciente)->nombres . ' ' . optional($prescription->historiaClinica?->paciente)->apellidos) ?: 'N/D' }}</td>
                            <td class="label">Edad</td>
                            <td class="value">{{ optional($prescription->historiaClinica?->paciente)->edad ?? 'N/D' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Especie</td>
                            <td class="value">{{ optional($prescription->historiaClinica?->paciente?->species)->name ?? 'N/D' }}</td>
                       
                            <td class="label">Raza</td>
                            <td class="value">{{ optional($prescription->historiaClinica?->paciente?->breed)->name ?? 'N/D' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Pelaje / color</td>
                            <td class="value">{{ optional($prescription->historiaClinica?->paciente)->color ?? 'N/D' }}</td>
                       
                            <td class="label">Peso</td>
                            <td class="value">
                                @php($patient = optional($prescription->historiaClinica?->paciente))
                                {{ $patient?->peso_formateado ?? 'N/D' }}
                            </td>
                        </tr>
                        
                    </table>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="rx-block">
                <div class="rx-head"><span>✚</span> Prescripción</div>
                <table class="rx-table">
                    <thead>
                        <tr>
                            <th style="width:26%;">Medicamento</th>
                            <th style="width:18%;">Dosis / Frecuencia</th>
                            <th style="width:12%;">Duración</th>
                            <th style="width:12%;">Cantidad</th>
                            <th>Instrucciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($prescription->items as $item)
                            <tr>
                                <td>
                                    <div class="name">{{ $item->is_manual ? $item->manual_name : optional($item->product)->name }}</div>
                                    @if($item->is_manual)
                                        <div class="muted">No facturable</div>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ $item->dose ?: 'Dosis N/D' }}</div>
                                    <div class="muted">{{ $item->frequency ?: 'Frec. N/D' }}</div>
                                </td>
                                <td>{{ $item->duration_days ? $item->duration_days . ' días' : 'N/D' }}</td>
                                <td>{{ $item->qty_requested }}</td>
                                <td>{{ $item->instructions ?: 'Sin instrucciones adicionales.' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="signature">
                <div class="signature-line"></div>
                <div style="font-weight:700;">{{ optional($prescription->professional)->name ?? 'Profesional N/D' }}</div>
                <div class="tiny">Firma y sello</div>
            </div>
        </div>

        <div class="footer">
            <div>
                <div style="font-weight:700; color: var(--purple);">Gracias por confiar en nosotros</div>
                <div class="tiny">Para dudas o seguimiento contáctanos por WhatsApp.</div>
            </div>
            <div style="text-align:right;">
                <div class="tiny">Recetario · {{ now()->format('d/m/Y') }}</div>
                <div class="tiny">{{ config('app.name') }}</div>
            </div>
        </div>
    </div>
</body>
</html>
