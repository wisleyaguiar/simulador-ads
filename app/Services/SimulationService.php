<?php

namespace App\Services;

class SimulationService
{
    public function calculateNetBudget(float $gross, bool $deductTax): float
    {
        return $deductTax ? $gross * (1 - 0.1215) : $gross;
    }

    public function getSeasonalityMultiplier(int $month): float
    {
        return match ($month) {
            11 => 1.5,
            12 => 1.3,
            1 => 0.85,
            default => 1.0,
        };
    }

    public function adjustCostsForSeason(float $cpm, float $cpc, int $month): array
    {
        $multiplier = $this->getSeasonalityMultiplier($month);
        return [
            'cpm' => $cpm * $multiplier,
            'cpc' => $cpc * $multiplier,
        ];
    }

    public function applyMaturityMultiplier(float $ctr, float $convRate, string $level): array
    {
        if ($level === 'Iniciante') {
            return [
                'ctr' => $ctr * 0.85,
                'convRate' => $convRate * 0.85,
            ];
        }
        if ($level === 'Avançado') {
            return [
                'ctr' => $ctr * 1.10,
                'convRate' => $convRate * 1.10,
            ];
        }
        
        // Intermediário
        return [
            'ctr' => $ctr,
            'convRate' => $convRate,
        ];
    }

    public function calculateMetrics(float $budget, float $cpm, float $ctr, float $convRate): array
    {
        $impressions = $cpm > 0 ? ($budget / $cpm) * 1000 : 0;
        $clicks = $impressions * $ctr;
        $leads = $clicks * $convRate;
        $cpa = $leads > 0 ? $budget / $leads : 0;

        return [
            'impressions' => round($impressions),
            'clicks' => round($clicks),
            'leads' => round($leads),
            'cpa' => round($cpa, 2),
        ];
    }

    public function generateScenarios(float $budget, float $adjustedCpm, float $adjustedCtr, float $adjustedConvRate): array
    {
        return [
            'conservative' => $this->calculateMetrics($budget, $adjustedCpm, $adjustedCtr * 0.8, $adjustedConvRate * 0.8),
            'realistic' => $this->calculateMetrics($budget, $adjustedCpm, $adjustedCtr * 1.0, $adjustedConvRate * 1.0),
            'optimistic' => $this->calculateMetrics($budget, $adjustedCpm, $adjustedCtr * 1.3, $adjustedConvRate * 1.3),
        ];
    }
}
