<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroomingReportRequest;
use App\Models\Grooming;
use App\Services\GroomingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GroomingReportController extends Controller
{
    public function create(Grooming $grooming): View
    {
        return view('groomings.report', compact('grooming'));
    }

    public function store(GroomingReportRequest $request, Grooming $grooming, GroomingService $service): RedirectResponse
    {
        $service->finalizeWithReport($grooming, $request->validated(), auth()->id());

        return redirect()
            ->route('groomings.show', $grooming)
            ->with('success', 'Informe guardado y servicio finalizado.');
    }
}
