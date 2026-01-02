<?php

namespace App\Http\Controllers;

use App\Models\GeoDepartment;
use Illuminate\Http\JsonResponse;

class GeoController extends Controller
{
    public function municipalities(GeoDepartment $department): JsonResponse
    {
        return response()->json($department->municipalities()->orderBy('name')->get());
    }
}
