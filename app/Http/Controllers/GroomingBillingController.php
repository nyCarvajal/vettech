<?php

namespace App\Http\Controllers;

use App\Models\Grooming;
use App\Services\GroomingBillingService;
use Illuminate\Http\RedirectResponse;
use InvalidArgumentException;

class GroomingBillingController extends Controller
{
    public function charge(Grooming $grooming, GroomingBillingService $service): RedirectResponse
    {
        try {
            $sale = $service->createOrAttachSaleItem($grooming);
        } catch (InvalidArgumentException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        if (! $sale) {
            return back()->with('info', 'Módulo de ventas no disponible en este entorno.');
        }

        return back()->with('success', 'Cobro generado en la venta #' . $sale->id);
    }
}
