<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket {{ $invoice->full_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 0; padding: 16px; }
        h1, h2 { margin: 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 4px 0; }
        .text-right { text-align: right; }
        .divider { border-top: 1px dashed #999; margin: 8px 0; }
    </style>
</head>
<body>
    <h1>{{ config('app.name') }}</h1>
    <p>Factura: {{ $invoice->full_number }}</p>
    <p>Fecha: {{ $invoice->issued_at?->format('d/m/Y H:i') }}</p>
    <p>Cliente: {{ $invoice->owner->name ?? 'N/A' }}</p>

    <div class="divider"></div>

    <table>
        <thead>
            <tr>
                <th align="left">Descripción</th>
                <th align="center">Cant.</th>
                <th align="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->lines as $line)
                <tr>
                    <td>{{ $line->description }}</td>
                    <td class="text-right">{{ number_format($line->quantity, 2) }}</td>
                    <td class="text-right">{{ number_format($line->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <table>
        <tr>
            <td>Subtotal</td>
            <td class="text-right">{{ number_format($invoice->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td>Descuentos</td>
            <td class="text-right">{{ number_format($invoice->discount_total, 2) }}</td>
        </tr>
        <tr>
            <td>Impuestos</td>
            <td class="text-right">{{ number_format($invoice->tax_total, 2) }}</td>
        </tr>
        <tr>
            <td>Comisiones</td>
            <td class="text-right">{{ number_format($invoice->commission_total, 2) }}</td>
        </tr>
        <tr>
            <td><strong>Total</strong></td>
            <td class="text-right"><strong>{{ number_format($invoice->total, 2) }}</strong></td>
        </tr>
    </table>

    <div class="divider"></div>

    <p>Pagos:</p>
    <ul>
        @foreach($invoice->payments as $payment)
            <li>{{ ucfirst($payment->method) }}: {{ number_format($payment->amount, 2) }}</li>
        @endforeach
    </ul>
    <p>Devueltas: {{ number_format($invoice->change_total, 2) }}</p>

    <p>Gracias por su compra.</p>
</body>
</html>
