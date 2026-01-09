<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arqueo de caja {{ $closure->date->format('d/m/Y') }}</title>
    <style>
        @page {
            size: letter;
            margin: 12mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #111827;
        }
        h1, h2, h3 {
            margin: 0;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }
        .header img {
            max-height: 48px;
        }
        .divider {
            border-top: 1px dashed #9ca3af;
            margin: 12px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 4px 0;
        }
        .text-right {
            text-align: right;
        }
        .muted {
            color: #6b7280;
        }
        .print-actions {
            display: flex;
            gap: 8px;
            margin-bottom: 10px;
        }
        .print-actions button,
        .print-actions a {
            border: 1px solid #d1d5db;
            background: #fff;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            text-decoration: none;
            color: #111827;
        }
        @media print {
            .print-actions { display: none; }
        }
    </style>
</head>
<body>
    <div class="print-actions">
        <button type="button" onclick="window.print()">Imprimir / Guardar PDF</button>
        <a href="{{ route('cash.closures.show', $closure) }}">Volver</a>
    </div>

    <div class="header">
        <div>
            <h1>{{ $clinic->nombre ?? config('app.name') }}</h1>
            <p class="muted">NIT: {{ $clinic->nit ?? 'N/A' }}</p>
        </div>
        @if (!empty($clinic?->logo_url))
            <img src="{{ $clinic->logo_url }}" alt="Logo">
        @endif
    </div>

    <h2>Arqueo de caja</h2>
    <p class="muted">Fecha: {{ $closure->date->format('d/m/Y') }} · Elaboró: {{ $closure->user->nombre ?? $closure->user->name ?? 'Usuario #' . $closure->user_id }}</p>

    <div class="divider"></div>

    <h3>Resumen por método</h3>
    <table>
        <thead>
            <tr>
                <th align="left">Método</th>
                <th align="right">Esperado</th>
                <th align="right">Contado</th>
                <th align="right">Diferencia</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Efectivo</td>
                <td class="text-right">$ {{ number_format($closure->expected_cash, 2, ',', '.') }}</td>
                <td class="text-right">$ {{ number_format($closure->counted_cash, 2, ',', '.') }}</td>
                <td class="text-right">$ {{ number_format($closure->counted_cash - $closure->expected_cash, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Tarjeta</td>
                <td class="text-right">$ {{ number_format($closure->expected_card ?? 0, 2, ',', '.') }}</td>
                <td class="text-right">$ {{ number_format($closure->counted_card ?? 0, 2, ',', '.') }}</td>
                <td class="text-right">$ {{ number_format(($closure->counted_card ?? 0) - ($closure->expected_card ?? 0), 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Transferencia</td>
                <td class="text-right">$ {{ number_format($closure->expected_transfer ?? 0, 2, ',', '.') }}</td>
                <td class="text-right">$ {{ number_format($closure->counted_transfer ?? 0, 2, ',', '.') }}</td>
                <td class="text-right">$ {{ number_format(($closure->counted_transfer ?? 0) - ($closure->expected_transfer ?? 0), 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Total</strong></td>
                <td class="text-right"><strong>$ {{ number_format($closure->total_expected, 2, ',', '.') }}</strong></td>
                <td class="text-right"><strong>$ {{ number_format($closure->total_counted, 2, ',', '.') }}</strong></td>
                <td class="text-right"><strong>$ {{ number_format($closure->difference, 2, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="divider"></div>

    <h3>Resumen del día</h3>
    <table>
        <tbody>
            <tr>
                <td>Ingresos</td>
                <td class="text-right">$ {{ number_format($summary['expected']['total'], 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Egresos</td>
                <td class="text-right">$ {{ number_format($summary['expenses']['total'], 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Neto</strong></td>
                <td class="text-right"><strong>$ {{ number_format($summary['net'], 2, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="divider"></div>

    <h3>Detalle de pagos</h3>
    <table>
        <thead>
            <tr>
                <th align="left">Hora</th>
                <th align="left">Cliente</th>
                <th align="left">Método</th>
                <th align="right">Valor</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($summary['payments'] as $payment)
                <tr>
                    <td>{{ $payment['time'] ?? '--' }}</td>
                    <td>{{ $payment['client'] }}</td>
                    <td>{{ ucfirst($payment['method']) }}</td>
                    <td class="text-right">$ {{ number_format($payment['amount'], 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <h3>Detalle de gastos</h3>
    <table>
        <thead>
            <tr>
                <th align="left">Hora</th>
                <th align="left">Categoría</th>
                <th align="right">Valor</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($summary['expenses']['items'] as $expense)
                <tr>
                    <td>{{ $expense['time'] ?? '--' }}</td>
                    <td>{{ $expense['category'] }}</td>
                    <td class="text-right">$ {{ number_format($expense['amount'], 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="muted">Sin gastos registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="divider"></div>

    <table>
        <tr>
            <td>Elaboró: ___________________________</td>
            <td class="text-right">Revisó: ___________________________</td>
        </tr>
    </table>
</body>
</html>
