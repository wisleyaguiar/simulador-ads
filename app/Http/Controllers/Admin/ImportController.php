<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DataHealthService;
use App\Services\ImportService;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function __construct(
        private ImportService $importService,
        private DataHealthService $healthService
    ) {}

    public function importSegments(\App\Http\Requests\ImportCsvRequest $request)
    {
        $this->importService->importSegments($request->file('file')->getRealPath());
        return response()->json(['message' => 'Segments imported successfully']);
    }

    public function importRegions(\App\Http\Requests\ImportCsvRequest $request)
    {
        $this->importService->importRegions($request->file('file')->getRealPath());
        return response()->json(['message' => 'Regions imported successfully']);
    }

    public function dataHealth()
    {
        return response()->json([
            'segments' => $this->healthService->getSegmentsHealth(),
            'regions' => $this->healthService->getRegionsHealth(),
            'overall_status' => $this->healthService->getOverallStatus(),
        ]);
    }
}
