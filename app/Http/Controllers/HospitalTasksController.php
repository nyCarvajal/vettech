<?php

namespace App\Http\Controllers;

use App\Http\Requests\HospitalTaskRequest;
use App\Models\HospitalTask;
use App\Services\HospitalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HospitalTasksController extends Controller
{
    public function __construct(private HospitalService $hospitalService)
    {
    }

    public function create(): View
    {
        return view('hospital.tasks.create');
    }

    public function store(HospitalTaskRequest $request): RedirectResponse
    {
        HospitalTask::create($request->validated() + ['created_by' => $request->user()->id]);
        return redirect()->route('hospital.board')->with('status', 'Tarea creada');
    }
}
