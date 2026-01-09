<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #0f172a;
            margin: 0;
        }
        .page {
            padding: 28px 32px;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header-title {
            font-size: 20px;
            font-weight: 700;
        }
        .header-meta {
            font-size: 11px;
            color: #475569;
            text-align: right;
        }
        .section {
            margin-bottom: 18px;
        }
        .section-title {
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 8px;
            color: #1e293b;
        }
        .info-grid {
            width: 100%;
            border-collapse: collapse;
        }
        .info-grid td {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .info-label {
            font-weight: 600;
            color: #334155;
            width: 30%;
            background-color: #f8fafc;
        }
        .consent-body {
            padding: 14px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background-color: #ffffff;
        }
        .signature {
            margin-top: 18px;
        }
        .signature-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        .signature-cell {
            display: table-cell;
            width: 33%;
            padding: 8px;
            vertical-align: top;
        }
        .signature-card {
            border: 1px solid #e2e8f0;
            padding: 10px 12px;
            border-radius: 8px;
            margin-bottom: 10px;
            min-height: 120px;
        }
        .signature-role {
            font-weight: 700;
            margin-bottom: 6px;
        }
        .signature-line {
            border-bottom: 1px solid #94a3b8;
            height: 60px;
        }
        .signature img {
            max-height: 90px;
            margin-top: 6px;
        }
        .text-sm {
            font-size: 11px;
            color: #475569;
        }
        .text-gray-500 {
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <div>
                <div class="header-title">Consentimiento {{ $consent->code }}</div>
                <div class="text-sm">Plantilla: {{ $consent->template->name ?? 'Sin plantilla' }}</div>
            </div>
            <div class="header-meta">
                <div>Estado: {{ ucfirst($consent->status) }}</div>
                <div>Firmado: {{ optional($consent->signed_at)->format('Y-m-d H:i') ?? 'Pendiente' }}</div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Datos del tutor</div>
            <table class="info-grid">
                <tr>
                    <td class="info-label">Nombre completo</td>
                    <td>{{ $consent->owner_snapshot['full_name'] ?? '' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Documento</td>
                    <td>{{ $consent->owner_snapshot['document'] ?? '' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Teléfono</td>
                    <td>{{ $consent->owner_snapshot['phone'] ?? '' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Correo</td>
                    <td>{{ $consent->owner_snapshot['email'] ?? '' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Dirección</td>
                    <td>{{ $consent->owner_snapshot['address'] ?? '' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Ciudad</td>
                    <td>{{ $consent->owner_snapshot['city'] ?? '' }}</td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title">Datos del paciente</div>
            <table class="info-grid">
                <tr>
                    <td class="info-label">Nombre</td>
                    <td>{{ $consent->pet_snapshot['name'] ?? '' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Especie</td>
                    <td>{{ $consent->pet_snapshot['species'] ?? '' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Raza</td>
                    <td>{{ $consent->pet_snapshot['breed'] ?? '' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Sexo</td>
                    <td>{{ $consent->pet_snapshot['sex'] ?? '' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Edad</td>
                    <td>{{ $consent->pet_snapshot['age'] ?? '' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Peso</td>
                    <td>{{ $consent->pet_snapshot['weight'] ?? '' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Color</td>
                    <td>{{ $consent->pet_snapshot['color'] ?? '' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Microchip</td>
                    <td>{{ $consent->pet_snapshot['microchip'] ?? '' }}</td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title">Detalle del consentimiento</div>
            <div class="consent-body">{!! $consent->merged_body_html !!}</div>
        </div>

        <div class="signature">
            <div class="section-title">Firmas</div>
            @php
                $requiredSigners = collect($consent->template->required_signers ?? []);
                $signerLabels = ['owner' => 'Tutor', 'vet' => 'Médico', 'witness' => 'Testigo'];
                $signaturesByRole = $consent->signatures->keyBy('signer_role');
            @endphp

            @if($requiredSigners->isNotEmpty())
                <div class="signature-grid">
                    @foreach($requiredSigners as $role)
                        @php($signature = $signaturesByRole->get($role))
                        <div class="signature-cell">
                            <div class="signature-card">
                                <div class="signature-role">{{ $signerLabels[$role] ?? ucfirst($role) }}</div>
                                <div class="signature-line">
                                    @if($signature)
                                        <img src="{{ Storage::disk('consents')->path($signature->signature_image_path) }}" alt="firma">
                                    @endif
                                </div>
                                <div class="text-sm">{{ $signature?->signer_name ?? '' }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                @forelse($consent->signatures as $signature)
                    <div class="signature-card">
                        <div><strong>{{ $signature->signer_name }}</strong> ({{ $signature->signer_role }})</div>
                        <div class="text-sm">{{ $signature->signed_at }} · {{ $signature->ip_address }} · {{ $signature->method }}</div>
                        <img src="{{ Storage::disk('consents')->path($signature->signature_image_path) }}" alt="firma">
                    </div>
                @empty
                    <div class="text-sm text-gray-500">Sin firmas registradas.</div>
                @endforelse
            @endif
        </div>
    </div>
</body>
</html>
