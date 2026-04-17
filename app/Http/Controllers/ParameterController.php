<?php

namespace App\Http\Controllers;

use App\Models\Region;
use App\Models\Segment;

class ParameterController extends Controller
{
    public function index()
    {
        return response()->json([
            'segments' => Segment::all(),
            'regions' => Region::all(),
        ]);
    }
}
