<?php

namespace App\Http\Controllers;

use App\Models\HandoffNote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HandoffController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'stay_id' => 'required|exists:hospital_stays,id',
            'shift_instance_id' => 'required|exists:shift_instances,id',
            'summary' => 'required|string',
            'pending' => 'nullable|string',
            'alerts' => 'nullable|string',
        ]);

        HandoffNote::create($data + ['author_id' => $request->user()->id]);

        return back()->with('status', 'Entrega de turno registrada');
    }
}
