<?php

namespace App\Http\Controllers;

use App\Http\Requests\CashMovementRequest;
use App\Models\CashMovement;
use App\Models\CashSession;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;

class CashMovementsController extends Controller
{
    public function __construct(private AuditService $auditService)
    {
    }

    public function store(CashMovementRequest $request): RedirectResponse
    {
        $session = CashSession::findOrFail($request->input('cash_session_id'));
        $movement = CashMovement::create($request->validated() + ['created_by' => $request->user()->id]);
        $this->auditService->logChange('cash.movement', 'cash_session', $session->id, [], $movement->toArray(), $request->user());
        return back()->with('status', 'Movimiento registrado');
    }
}
