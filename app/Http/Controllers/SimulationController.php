<?php

namespace App\Http\Controllers;

use App\Models\Simulation;
use App\Services\SimulationService;
use Illuminate\Http\Request;

class SimulationController extends Controller
{
    public function __construct(private SimulationService $simulationService) {}

    public function index(Request $request)
    {
        return response()->json(
            $request->user()->simulations()->with(['segment', 'region'])->orderBy('created_at', 'desc')->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'budget' => 'required|numeric|min:0',
            'payment_type' => 'required|string',
            'campaign_days' => 'required|integer|min:1',
            'region_id' => 'required|exists:regions,id',
            'segment_id' => 'required|exists:segments,id',
            'goal' => 'required|string',
            'maturity_level' => 'required|string|in:Iniciante,Intermediário,Avançado',
            'campaign_month' => 'required|integer|between:1,12',
            'deduct_tax' => 'required|boolean',
        ]);

        $grossBudget = $validated['budget'];
        $netBudget = $this->simulationService->calculateNetBudget($grossBudget, $validated['deduct_tax']);

        $region = \App\Models\Region::findOrFail($validated['region_id']);
        $segment = \App\Models\Segment::findOrFail($validated['segment_id']);

        $seasonalCosts = $this->simulationService->adjustCostsForSeason($region->avg_cpm, $segment->avg_cpc, $validated['campaign_month']);
        $adjustedMetrics = $this->simulationService->applyMaturityMultiplier($segment->avg_ctr, $segment->avg_conversion_rate, $validated['maturity_level']);

        $scenarios = $this->simulationService->generateScenarios($netBudget, $seasonalCosts['cpm'], $adjustedMetrics['ctr'], $adjustedMetrics['convRate']);

        $simulation = $request->user()->simulations()->create([
            'budget' => $grossBudget,
            'payment_type' => $validated['payment_type'],
            'campaign_days' => $validated['campaign_days'],
            'region_id' => $region->id,
            'segment_id' => $segment->id,
            'goal' => $validated['goal'],
            'maturity_level' => $validated['maturity_level'],
            'campaign_month' => $validated['campaign_month'],
            'results_json' => [
                'net_budget' => $netBudget,
                'scenarios' => $scenarios,
                'adjusted_costs' => $seasonalCosts,
                'adjusted_metrics' => $adjustedMetrics,
            ],
        ]);

        return response()->json($simulation, 201);
    }

    public function destroy(Request $request, $id)
    {
        $simulation = $request->user()->simulations()->findOrFail($id);
        $simulation->delete();
        return response()->json(['message' => 'Simulation deleted successfully']);
    }
}
