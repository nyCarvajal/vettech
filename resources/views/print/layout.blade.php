<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Imprimible')</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #111827;
            margin: 0;
        }
        .print-header {
            display: flex;
            gap: 16px;
            align-items: center;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .print-header img {
            max-height: 64px;
            object-fit: contain;
        }
        .print-header__info {
            font-size: 12px;
            color: #4b5563;
        }
        .print-footer {
            margin-top: 20px;
            border-top: 1px solid #e5e7eb;
            padding-top: 12px;
            font-size: 11px;
            color: #6b7280;
        }
    </style>
    @yield('styles')
</head>
<body>
    @php
        $logoUrl = $clinica->logo_path ? asset('storage/' . $clinica->logo_path) : asset('images/logo-dark.png');
        $displayName = $clinica->name ?? $clinica->nombre ?? config('app.name');
    @endphp
    <header class="print-header">
        <img src="{{ $logoUrl }}" alt="{{ $displayName }}">
        <div class="print-header__info">
            <strong>{{ $displayName }}</strong><br>
            @if ($clinica->nit)
                NIT: {{ $clinica->nit }}@if($clinica->dv) - {{ $clinica->dv }}@endif<br>
            @endif
            @if ($clinica->address ?? $clinica->direccion)
                Dirección: {{ $clinica->address ?? $clinica->direccion }}<br>
            @endif
            @if ($clinica->phone)
                Tel: {{ $clinica->phone }}<br>
            @endif
            @if ($clinica->email)
                Email: {{ $clinica->email }}<br>
            @endif
            @if ($clinica->header_note)
                <em>{{ $clinica->header_note }}</em>
            @endif
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="print-footer">
        @if ($clinica->footer_note)
            <p>{{ $clinica->footer_note }}</p>
        @endif
        @if ($clinica->invoice_footer_legal)
            <p>{{ $clinica->invoice_footer_legal }}</p>
        @endif
    </footer>
</body>
</html>
