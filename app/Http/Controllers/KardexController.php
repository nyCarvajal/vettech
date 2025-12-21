<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use Illuminate\View\View;

class KardexController extends Controller
{
    public function index(): View
    {
        $movements = StockMovement::with(['product', 'batch', 'user'])->latest()->paginate(20);
        return view('inventory.kardex.index', compact('movements'));
    }
}
