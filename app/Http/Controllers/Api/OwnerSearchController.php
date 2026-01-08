<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use Illuminate\Http\Request;

class OwnerSearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $query = $request->string('q')->toString();

        $owners = Owner::query()
            ->when($query !== '', function ($builder) use ($query) {
                $builder->where(function ($inner) use ($query) {
                    $inner->where('name', 'like', "%{$query}%")
                        ->orWhere('document', 'like', "%{$query}%")
                        ->orWhere('phone', 'like', "%{$query}%");
                });
            })
            ->orderBy('name')
            ->limit(15)
            ->get();

        return response()->json($owners->map(fn (Owner $owner) => [
            'id' => $owner->id,
            'text' => $owner->name,
            'document' => $owner->document,
            'phone' => $owner->phone,
        ]));
    }
}
