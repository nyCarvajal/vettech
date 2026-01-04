<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .signature { margin-top: 20px; }
        .signature img { max-height: 80px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Consentimiento {{ $consent->code }}</h1>
        <p>Firmado: {{ optional($consent->signed_at)->format('Y-m-d H:i') }}</p>
    </div>
    <h3>Datos del tutor</h3>
    <pre>{{ json_encode($consent->owner_snapshot, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
    <h3>Datos del paciente</h3>
    <pre>{{ json_encode($consent->pet_snapshot, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
    <hr>
    <div>{!! $consent->merged_body_html !!}</div>
    <div class="signature">
        <h3>Firmas</h3>
        @foreach($consent->signatures as $signature)
            <div style="margin-bottom:12px;">
                <p><strong>{{ $signature->signer_name }}</strong> ({{ $signature->signer_role }})</p>
                <p>{{ $signature->signed_at }} · {{ $signature->ip_address }} · {{ $signature->method }}</p>
                <img src="{{ public_path('storage/'.$signature->signature_image_path) }}" alt="firma">
            </div>
        @endforeach
    </div>
</body>
</html>
