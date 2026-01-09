@extends('print.layout')

@php
    $paper = request('paper', 'ticket');
    $isLetter = $paper === 'letter';
@endphp

@section('title', ($isLetter ? 'Factura' : 'Ticket') . ' ' . $invoice->full_number)

@section('styles')
    <style>
        :root {
            --page-margin: {{ $isLetter ? '12mm' : '4mm' }};
            --content-width: {{ $isLetter ? 'auto' : '80mm' }};
            --font-size: {{ $isLetter ? '12px' : '11px' }};
        }
        @page {
            size: {{ $isLetter ? 'letter' : '80mm auto' }};
            margin: var(--page-margin);
        }
        body { font-size: var(--font-size); padding: var(--page-margin); }
        .sheet { width: var(--content-width); }
        h1, h2 { margin: 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 4px 0; }
        .text-right { text-align: right; }
        .divider { border-top: 1px dashed #999; margin: 8px 0; }
        .print-actions { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 12px; }
        .print-actions a,
        .print-actions button {
            border: 1px solid #d1d5db;
            background: #fff;
            color: #111827;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            text-decoration: none;
        }
        .print-note { font-size: 11px; color: #6b7280; margin-top: 4px; }
        @media print {
            .print-actions { display: none; }
            body { padding: 0; }
        }
    </style>
@endsection

@section('content')
    <div class="sheet">
        <div class="print-actions">
            <a href="{{ route('invoices.print', [$invoice, 'paper' => 'letter']) }}">Carta (PDF)</a>
            <a href="{{ route('invoices.print', [$invoice, 'paper' => 'ticket']) }}">Ticket 80mm</a>
            <button type="button" onclick="window.print()">Imprimir / Guardar PDF</button>
        </div>
        <p class="print-note">Se abre en una pestaña nueva. Usa &quot;Guardar como PDF&quot; para generar el archivo.</p>

        <h1>{{ $clinica->name ?? $clinica->nombre ?? config('app.name') }}</h1>
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
    </div>
    <script>
        window.addEventListener('load', () => {
            setTimeout(() => window.print(), 300);
        });
    </script>
@endsection
