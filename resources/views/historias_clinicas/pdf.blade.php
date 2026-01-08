<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        :root {
            --mint: #8de3c4;
            --soft-purple: #b5a8ff;
            --deep-purple: #5a4ba8;
            --border: #dce7ef;
            --text: #0f172a;
            --muted: #475569;
            --bg: #f7f9fb;
        }

        body { font-family: DejaVu Sans, sans-serif; color: var(--text); background: var(--bg); margin: 20px; }
        h1,h2,h3 { margin: 0; }
        .pill { display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border-radius:999px; background: linear-gradient(135deg, var(--soft-purple), var(--mint)); color:#0b1021; font-weight:700; font-size:12px; text-transform: uppercase; letter-spacing: .5px; }
        .header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:16px; padding:14px; border-radius:12px; background: linear-gradient(135deg, rgba(181,168,255,.18), rgba(141,227,196,.18)); border:1px solid var(--border); }
        .logo { display:flex; align-items:center; gap:10px; }
        .logo-mark { width:42px; height:42px; border-radius:12px; background: radial-gradient(circle at 30% 30%, var(--mint), var(--soft-purple)); display:grid; place-items:center; box-shadow: 0 4px 12px rgba(90,75,168,.18); }
        .logo-mark svg { width:26px; height:26px; color:#0f172a; }
        .meta { text-align:right; color: var(--muted); font-size:12px; }
        .card { border:1px solid var(--border); border-radius:12px; padding:12px; margin-bottom:12px; background:#fff; box-shadow: 0 6px 14px rgba(16,24,40,.06); }
        .section-title { display:flex; align-items:center; gap:8px; color: var(--deep-purple); margin-bottom:8px; }
        .section-title svg { width:16px; height:16px; color: var(--deep-purple); }
        .muted { color: var(--muted); }
        .grid { display:grid; grid-template-columns: repeat(2, 1fr); gap:8px; }
        .tag { background: linear-gradient(135deg, rgba(181,168,255,.25), rgba(141,227,196,.25)); padding:8px 10px; border-radius:10px; font-size:12px; border:1px solid var(--border); }
        .field-label { background: var(--mint); border-radius: 999px; padding: 2px 8px; display:inline-block; font-weight:600; color: #0b1021; }
        .list { margin:0; padding:0; list-style:none; }
        .list-item { padding:8px 0; border-bottom:1px solid var(--border); }
        .list-item:last-child { border-bottom:none; }
        .table { width:100%; border-collapse: collapse; }
        .table th { background: linear-gradient(135deg, rgba(181,168,255,.35), rgba(141,227,196,.35)); color: var(--deep-purple); padding:8px; text-align:left; font-size:12px; }
        .table th.field-label { display: table-cell; }
        .table td { padding:8px; border-bottom:1px solid var(--border); font-size:12px; }
        .badge { display:inline-block; padding:4px 8px; border-radius:8px; background: rgba(90,75,168,.12); color: var(--deep-purple); font-size:11px; font-weight:700; text-transform: uppercase; letter-spacing:.4px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <div class="logo-mark">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 12a8 8 0 0 1 16 0c0 4.418-4 8-8 8-1.53 0-2.964-.43-4.18-1.18" />
                    <path d="M9 14h6" />
                    <path d="M12 11v6" />
                    <path d="M7.5 10.5h.01" />
                    <path d="M16.5 10.5h.01" />
                </svg>
            </div>
            <div>
                <div class="pill">Historial Clínico</div>
                <h1>Ficha #{{ $historiaClinica->id }}</h1>
                <p class="muted">Paciente: {{ trim(($historiaClinica->paciente->nombres ?? '') . ' ' . ($historiaClinica->paciente->apellidos ?? '')) }}</p>
            </div>
        </div>
        <div class="meta">
            <strong>Fecha de emisión</strong><br>
            {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <div class="card">
        <div class="section-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 20c4.418 0 8-3.09 8-7s-3.582-7-8-7-8 3.09-8 7c0 2.09 1.086 3.973 2.824 5.238" />
                <path d="M10 14h4" />
                <path d="M12 10v4" />
            </svg>
            <h3>Motivo y antecedentes</h3>
        </div>
        <table class="table">
            <tr>
                <th class="field-label" style="width:35%">Motivo de consulta</th>
                <td>{{ $historiaClinica->motivo_consulta ?: 'Sin registrar' }}</td>
            </tr>
            <tr>
                <th class="field-label">Enfermedad actual</th>
                <td>{{ $historiaClinica->enfermedad_actual ?: 'Sin registrar' }}</td>
            </tr>
            <tr>
                <th class="field-label">Antecedentes farmacológicos</th>
                <td>{{ $historiaClinica->antecedentes_farmacologicos ?: 'N/D' }}</td>
            </tr>
            <tr>
                <th class="field-label">Antecedentes patológicos</th>
                <td>{{ $historiaClinica->antecedentes_patologicos ?: 'N/D' }}</td>
            </tr>
        </table>
    </div>

    <div class="card">
        <div class="section-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M8 11a4 4 0 1 1 8 0c0 4-4 7-4 7s-4-3-4-7" />
                <path d="M12 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0" />
            </svg>
            <h3>Tutor</h3>
        </div>
        <p class="muted" style="margin-bottom:6px;">{{ optional($historiaClinica->paciente?->owner)->name }}</p>
        <div class="grid">
            <div class="tag"><strong class="field-label">Tel:</strong> {{ optional($historiaClinica->paciente?->owner)->phone ?: 'N/D' }}</div>
            <div class="tag"><strong class="field-label">WhatsApp:</strong> {{ optional($historiaClinica->paciente?->owner)->whatsapp ?: 'N/D' }}</div>
            <div class="tag"><strong class="field-label">Correo:</strong> {{ optional($historiaClinica->paciente?->owner)->email ?: 'N/D' }}</div>
        </div>
    </div>

    <div class="card">
        <div class="section-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M8.5 20h-2A2.5 2.5 0 0 1 4 17.5v-11A2.5 2.5 0 0 1 6.5 4H13l6 6v7.5A2.5 2.5 0 0 1 16.5 20H15" />
                <path d="M13 4v4a2 2 0 0 0 2 2h4" />
                <path d="M9.5 17.5 11 19l3.5-3.5" />
            </svg>
            <h3>Diagnósticos</h3>
        </div>
        @forelse($historiaClinica->diagnosticos as $diag)
            <div class="list-item">
                <strong>{{ $diag->descripcion }}</strong> <span class="badge">{{ $diag->codigo ?: 'Sin código' }}</span>
            </div>
        @empty
            <p class="muted">Sin diagnósticos.</p>
        @endforelse
    </div>

    <div class="card">
        <div class="section-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 7a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2z" />
                <path d="M16 3v4" />
                <path d="M8 3v4" />
                <path d="M4 11h16" />
                <path d="M8 15h2v2H8z" />
            </svg>
            <h3>Paraclínicos</h3>
        </div>
        @forelse($historiaClinica->paraclinicos as $item)
            <div class="list-item">
                <strong>{{ $item->nombre }}</strong>
                <div class="muted">{{ $item->resultado ?: 'Pendiente' }}</div>
            </div>
        @empty
            <p class="muted">Sin paraclínicos agregados.</p>
        @endforelse
    </div>

    @php
        $imageAttachments = $historiaClinica->adjuntos->where('file_type', 'image');
        $pdfAttachments = $historiaClinica->adjuntos->where('file_type', 'pdf');
    @endphp

    @if($imageAttachments->count() || $pdfAttachments->count())
        <div class="card">
            <div class="section-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 7h16v10H4z" />
                    <path d="M9 12h6" />
                    <path d="M9 9h6" />
                </svg>
                <h3>Adjuntos</h3>
            </div>

            @if($imageAttachments->count())
                <p class="muted" style="margin: 6px 0 4px 0;">Imágenes</p>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:8px;">
                    @foreach($imageAttachments as $image)
                        <div style="border:1px solid var(--border);border-radius:10px;padding:6px;text-align:center;">
                            <img src="{{ $image->cloudinary_secure_url }}" alt="{{ $image->titulo_limpio }}" style="width:100%;height:90px;object-fit:cover;border-radius:8px;">
                            <div style="font-size:11px;margin-top:4px;color:var(--muted);">{{ $image->titulo_limpio }}</div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($pdfAttachments->count())
                <p class="muted" style="margin: 12px 0 4px 0;">PDFs</p>
                <ul class="list">
                    @foreach($pdfAttachments as $pdf)
                        <li class="list-item">
                            <strong>{{ $pdf->titulo_limpio }}</strong>
                            <span class="badge">PDF</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    <div class="card">
        <div class="section-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M7 18H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2h-3" />
                <path d="M12 7v11" />
                <path d="M9 15 12 18 15 15" />
            </svg>
            <h3>Plan</h3>
        </div>
        <table class="table">
            <tr>
                <th class="field-label" style="width:30%">Análisis</th>
                <td>{{ $historiaClinica->analisis ?: 'N/D' }}</td>
            </tr>
            <tr>
                <th class="field-label">Procedimientos</th>
                <td>{{ $historiaClinica->plan_procedimientos ?: 'N/D' }}</td>
            </tr>
            <tr>
                <th class="field-label">Medicamentos</th>
                <td>{{ $historiaClinica->plan_medicamentos ?: 'N/D' }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
