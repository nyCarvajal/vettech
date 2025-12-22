<?php

namespace App\Http\Controllers;

use App\Models\Breed;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BreedsController extends Controller
{
    public function __invoke(Request $request)
    {
        $breeds = Breed::query()
            ->when($request->filled('species_id'), fn ($q) => $q->where('species_id', $request->integer('species_id')))
            ->orderBy('name')
            ->get(['id', 'name', 'species_id']);

        return JsonResource::collection($breeds);
    }
}
