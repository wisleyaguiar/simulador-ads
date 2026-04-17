<?php

namespace App\Services;

use App\Models\Region;
use App\Models\Segment;

class ImportService
{
    public function importSegments(string $filePath): void
    {
        $data = $this->parseCsv($filePath);
        foreach ($data as $row) {
            $score = isset($row['confidence_score']) ? (float) $row['confidence_score'] : 0.0;
            if ($score < 0.0 || $score > 1.0) {
                continue; // Rejeitar fora do range
            }
            // Conversão resiliente de %
            $ctr = $this->parsePercentage($row['avg_ctr'] ?? 0);
            $cr = $this->parsePercentage($row['avg_conversion_rate'] ?? 0);
            $cpc = (float) str_replace(',', '.', $row['avg_cpc'] ?? 0);

            Segment::updateOrCreate(
                ['name' => trim($row['name'])],
                [
                    'avg_ctr' => $ctr,
                    'avg_cpc' => $cpc,
                    'avg_conversion_rate' => $cr,
                    'confidence_score' => $score,
                    'technical_justification' => $row['technical_justification'] ?? null,
                ]
            );
        }
    }

    public function importRegions(string $filePath): void
    {
        $data = $this->parseCsv($filePath);
        foreach ($data as $row) {
            $score = isset($row['confidence_score']) ? (float) $row['confidence_score'] : 0.0;
            if ($score < 0.0 || $score > 1.0) {
                continue; // Rejeitar fora do range
            }
            // Sanitização de valores comma-separated
            $cpm = (float) str_replace(',', '.', $row['avg_cpm'] ?? 0);
            $totalPop = (int) str_replace(['.', ','], '', $row['total_population'] ?? 0);
            $audience = (int) str_replace(['.', ','], '', $row['reachable_audience'] ?? 0);

            Region::updateOrCreate(
                ['name' => trim($row['name'])],
                [
                    'total_population' => $totalPop,
                    'reachable_audience' => $audience,
                    'avg_cpm' => $cpm,
                    'confidence_score' => $score,
                ]
            );
        }
    }

    private function parseCsv(string $filePath): array
    {
        $rows = array_map('str_getcsv', file($filePath));
        $header = array_shift($rows);
        $csv = [];
        foreach ($rows as $row) {
            if (count($header) === count($row)) {
                $csv[] = array_combine($header, $row);
            }
        }
        return $csv;
    }

    private function parsePercentage($value): float
    {
        $value = (string) $value;
        $value = str_replace(['%', ','], ['', '.'], $value);
        $floatVal = (float) $value;
        
        // Exemplo: se o usuário digitou "15" querendo dizer 15%, convertemos para 0.15
        if ($floatVal > 1) {
            $floatVal = $floatVal / 100;
        }
        
        return $floatVal;
    }
}
