<?php

namespace App\Services;

use App\Models\Region;
use App\Models\Segment;
use Carbon\Carbon;

class DataHealthService
{
    public function getSegmentsHealth(): array
    {
        $lastUpdated = Segment::max('updated_at');
        if (!$lastUpdated) return $this->emptyHealth();

        $lastUpdated = Carbon::parse($lastUpdated);
        $days = $lastUpdated->diffInDays(now());
        $avgScore = Segment::avg('confidence_score');

        return [
            'last_updated' => $lastUpdated->toIso8601String(),
            'days_since_update' => $days,
            'avg_confidence_score' => round($avgScore, 2),
            'is_stale' => $days > 90,
        ];
    }

    public function getRegionsHealth(): array
    {
        $lastUpdated = Region::max('updated_at');
        if (!$lastUpdated) return $this->emptyHealth();

        $lastUpdated = Carbon::parse($lastUpdated);
        $days = $lastUpdated->diffInDays(now());
        $avgScore = Region::avg('confidence_score');

        return [
            'last_updated' => $lastUpdated->toIso8601String(),
            'days_since_update' => $days,
            'avg_confidence_score' => round($avgScore, 2),
            'is_stale' => $days > 180,
        ];
    }

    public function getOverallStatus(): string
    {
        $segmentsHealth = $this->getSegmentsHealth();
        $regionsHealth = $this->getRegionsHealth();

        if ($segmentsHealth['is_stale'] || $regionsHealth['is_stale']) {
            return 'critical';
        }

        if ($segmentsHealth['days_since_update'] > 60 || $regionsHealth['days_since_update'] > 150) {
            return 'warning';
        }

        return 'healthy';
    }

    private function emptyHealth(): array
    {
        return [
            'last_updated' => null,
            'days_since_update' => 0,
            'avg_confidence_score' => 0,
            'is_stale' => false,
        ];
    }
}
