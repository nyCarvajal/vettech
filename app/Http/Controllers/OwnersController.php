<?php

namespace App\Http\Controllers;

use App\Http\Requests\OwnerRequest;
use App\Models\Owner;
use App\Services\TimelineService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnersController extends Controller
{
    public function __construct(private TimelineService $timelineService)
    {
    }

    public function index(Request $request): View
    {
        $query = Owner::query();

        if ($search = $request->string('q')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('document', 'like', "%{$search}%");
            });
        }

        $owners = $query->withCount('patients')->orderBy('name')->paginate(15)->withQueryString();

        return view('owners.index', compact('owners', 'search'));
    }

    public function create(): View
    {
        return view('owners.form', ['owner' => new Owner()]);
    }

    public function store(OwnerRequest $request): RedirectResponse
    {
        $owner = Owner::create($request->validated());

        return redirect()->route('owners.show', $owner)->with('status', 'Tutor creado correctamente');
    }

    public function show(Owner $owner): View
    {
        $owner->load(['patients.species', 'patients.breed', 'patients.lastEncounter']);

        $timelines = $owner->patients->mapWithKeys(function ($patient) {
            return [$patient->id => $this->timelineService->forPatient($patient, ['limit' => 5])];
        });

        return view('owners.show', [
            'owner' => $owner,
            'timelines' => $timelines,
        ]);
    }

    public function edit(Owner $owner): View
    {
        return view('owners.form', compact('owner'));
    }

    public function update(OwnerRequest $request, Owner $owner): RedirectResponse
    {
        $owner->update($request->validated());

        return redirect()->route('owners.show', $owner)->with('status', 'Tutor actualizado');
    }
}
