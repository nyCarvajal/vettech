<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .section { margin-bottom: 12px; }
        .title { font-size: 20px; font-weight: bold; margin-bottom: 10px; }
        .box { border: 1px solid #ccc; padding: 10px; }
    </style>
</head>
<body>
    <div class="title">Certificado de Salud para Viaje ({{ strtoupper($certificate->type) }})</div>
    <div class="section box">
        <strong>Clínica:</strong> {{ $certificate->clinic_name }} - {{ $certificate->clinic_nit }}<br>
        {{ $certificate->clinic_address }} - {{ $certificate->clinic_phone }} - {{ $certificate->clinic_city }}
    </div>
    <div class="section box">
        <strong>Código:</strong> {{ $certificate->code }} | <strong>Estado:</strong> {{ $certificate->status }}<br>
        <strong>Emisión:</strong> {{ optional($certificate->issued_at)->format('Y-m-d') }} | <strong>Vigencia:</strong> {{ optional($certificate->expires_at)->format('Y-m-d') }}
    </div>
    <div class="section box">
        <strong>Tutor:</strong> {{ $certificate->owner_name }} ({{ $certificate->owner_document_number }})<br>
        <strong>Mascota:</strong> {{ $certificate->pet_name }} / {{ $certificate->pet_species }} / {{ $certificate->pet_breed }}
    </div>
    <div class="section box">
        <strong>Viaje:</strong> {{ $certificate->travel_departure_date?->format('Y-m-d') }} - {{ $certificate->travel_departure_time }}
    </div>
    <div class="section box">
        <strong>Declaración:</strong>
        <p>{{ $certificate->declaration_text }}</p>
    </div>
    <div class="section box">
        <strong>Vacunas:</strong>
        <ul>
            @foreach($certificate->vaccinations as $vac)
                <li>{{ $vac->vaccine_name }} ({{ $vac->applied_at?->format('Y-m-d') }})</li>
            @endforeach
        </ul>
    </div>
    <div class="section box">
        <strong>Desparasitación:</strong>
        <ul>
            @foreach($certificate->dewormings as $dew)
                <li>{{ $dew->kind }} - {{ $dew->product_name }} ({{ $dew->applied_at?->format('Y-m-d') }})</li>
            @endforeach
        </ul>
    </div>
    <div class="section box">
        <strong>Extras:</strong>
        <ul>
            @foreach(($certificate->extras ?? []) as $key => $value)
                <li>{{ $key }}: {{ $value }}</li>
            @endforeach
        </ul>
    </div>
    <div class="section">
        <strong>MV:</strong> {{ $certificate->vet_name }} - {{ $certificate->vet_license }}
    </div>
</body>
</html>
