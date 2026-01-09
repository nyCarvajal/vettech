<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Owner;
use App\Models\User;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('ensureRole:admin');
    }

    public function index()
    {
        $expenses = Expense::query()->orderByDesc('paid_at')->paginate(20);

        return view('expenses.index', [
            'expenses' => $expenses,
        ]);
    }

    public function create()
    {
        return view('expenses.create', [
            'owners' => Owner::query()->select('id', 'name')->orderBy('name')->get(),
            'users' => User::query()->select('id', 'nombre', 'apellidos')->orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data['user_id'] = $data['user_id'] ?? $request->user()->id;
        $data['tenant_id'] = $request->user()->tenant_id ?? null;

        Expense::create($data);

        return redirect()->route('expenses.index')->with('success', 'Gasto registrado.');
    }

    public function edit(Expense $expense)
    {
        return view('expenses.edit', [
            'expense' => $expense,
            'owners' => Owner::query()->select('id', 'name')->orderBy('name')->get(),
            'users' => User::query()->select('id', 'nombre', 'apellidos')->orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, Expense $expense)
    {
        $data = $this->validatedData($request);
        $expense->update($data);

        return redirect()->route('expenses.index')->with('success', 'Gasto actualizado.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Gasto eliminado.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'category' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'amount' => ['required', 'numeric', 'min:0'],
            'paid_at' => ['required', 'date'],
            'payment_method' => ['nullable', 'string', 'max:100'],
            'user_id' => ['nullable', 'integer'],
            'owner_id' => ['nullable', 'integer'],
        ]);
    }
}
