<?php

namespace App\Http\Controllers;

use App\Models\Cage;
use Illuminate\View\View;

class HospitalBoardController extends Controller
{
    public function __invoke(): View
    {
        $cages = Cage::with(['stays' => function ($query) {
            $query->where('status', 'active')->latest();
        }, 'stays.tasks.logs'])->get();

        return view('hospital.board', compact('cages'));
    }
}
