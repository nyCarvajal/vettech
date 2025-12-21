<?php

namespace App\Http\Controllers;

use App\Http\Requests\CashSessionRequest;
use App\Models\CashSession;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CashSessionsController extends Controller
{
    public function __construct(private AuditService $auditService)
    {
    }

    public function index(): View
    {
        $sessions = CashSession::with('register')->latest()->paginate(10);
        return view('cash.sessions.index', compact('sessions'));
    }

    public function store(CashSessionRequest $request): RedirectResponse
    {
        $session = CashSession::create($request->validated() + [
            'opened_by' => $request->user()->id,
            'opened_at' => now(),
            'status' => 'open',
        ]);
        return redirect()->route('cash.sessions.index')->with('status', 'Caja abierta');
    }

    public function close(CashSession $cashSession): RedirectResponse
    {
        $before = $cashSession->toArray();
        $cashSession->update([
            'closed_at' => now(),
            'status' => 'closed',
            'closing_amount_expected' => $cashSession->movements()->where('type', 'income')->sum('amount') - $cashSession->movements()->where('type', 'expense')->sum('amount') + $cashSession->opening_amount,
            'closing_amount_counted' => request('closing_amount_counted'),
            'notes' => request('notes'),
        ]);
        $this->auditService->logChange('cash.close', 'cash_session', $cashSession->id, $before, $cashSession->toArray(), request()->user());

        return redirect()->route('cash.sessions.index')->with('status', 'Caja cerrada');
    }
}
