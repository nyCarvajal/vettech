<?php

namespace App\Http\Controllers;

use App\Models\Grooming;
use App\Services\GroomingService;
use Illuminate\Http\RedirectResponse;

class GroomingStatusController extends Controller
{
    public function start(Grooming $grooming, GroomingService $service): RedirectResponse
    {
        $service->startGrooming($grooming);

        return back()->with('success', 'Servicio iniciado.');
    }

    public function cancel(Grooming $grooming, GroomingService $service): RedirectResponse
    {
        $service->cancelGrooming($grooming);

        return back()->with('success', 'Servicio cancelado.');
    }
}
