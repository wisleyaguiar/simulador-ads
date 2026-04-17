<?php

namespace Database\Seeders;

use App\Models\Segment;
use Illuminate\Database\Seeder;

class SegmentSeeder extends Seeder
{
    /**
     * Seed the segments table with initial benchmark data.
     * Confidence score default: 0.70 (estimated data).
     */
    public function run(): void
    {
        $segments = [
            [
                'name' => 'Saúde e Bem-Estar',
                'avg_ctr' => 0.0145,    // 1.45%
                'avg_cpc' => 3.20,      // R$ 3.20
                'avg_conversion_rate' => 0.0280, // 2.80%
                'confidence_score' => 0.70,
            ],
            [
                'name' => 'Imobiliário',
                'avg_ctr' => 0.0098,    // 0.98%
                'avg_cpc' => 5.80,      // R$ 5.80
                'avg_conversion_rate' => 0.0120, // 1.20%
                'confidence_score' => 0.70,
            ],
            [
                'name' => 'E-commerce Moda',
                'avg_ctr' => 0.0180,    // 1.80%
                'avg_cpc' => 2.50,      // R$ 2.50
                'avg_conversion_rate' => 0.0250, // 2.50%
                'confidence_score' => 0.70,
            ],
            [
                'name' => 'SaaS B2B',
                'avg_ctr' => 0.0110,    // 1.10%
                'avg_cpc' => 8.50,      // R$ 8.50
                'avg_conversion_rate' => 0.0150, // 1.50%
                'confidence_score' => 0.70,
            ],
            [
                'name' => 'Educação Online',
                'avg_ctr' => 0.0165,    // 1.65%
                'avg_cpc' => 2.80,      // R$ 2.80
                'avg_conversion_rate' => 0.0320, // 3.20%
                'confidence_score' => 0.70,
            ],
            [
                'name' => 'Alimentação e Delivery',
                'avg_ctr' => 0.0210,    // 2.10%
                'avg_cpc' => 1.90,      // R$ 1.90
                'avg_conversion_rate' => 0.0380, // 3.80%
                'confidence_score' => 0.70,
            ],
            [
                'name' => 'Automotivo',
                'avg_ctr' => 0.0120,    // 1.20%
                'avg_cpc' => 4.50,      // R$ 4.50
                'avg_conversion_rate' => 0.0100, // 1.00%
                'confidence_score' => 0.70,
            ],
            [
                'name' => 'Finanças e Seguros',
                'avg_ctr' => 0.0095,    // 0.95%
                'avg_cpc' => 9.80,      // R$ 9.80
                'avg_conversion_rate' => 0.0090, // 0.90%
                'confidence_score' => 0.70,
            ],
            [
                'name' => 'Turismo e Hotelaria',
                'avg_ctr' => 0.0155,    // 1.55%
                'avg_cpc' => 3.50,      // R$ 3.50
                'avg_conversion_rate' => 0.0200, // 2.00%
                'confidence_score' => 0.70,
            ],
            [
                'name' => 'Fitness e Esportes',
                'avg_ctr' => 0.0190,    // 1.90%
                'avg_cpc' => 2.20,      // R$ 2.20
                'avg_conversion_rate' => 0.0300, // 3.00%
                'confidence_score' => 0.70,
            ],
            [
                'name' => 'Beleza e Cosméticos',
                'avg_ctr' => 0.0200,    // 2.00%
                'avg_cpc' => 2.10,      // R$ 2.10
                'avg_conversion_rate' => 0.0350, // 3.50%
                'confidence_score' => 0.70,
            ],
            [
                'name' => 'Tecnologia e Eletrônicos',
                'avg_ctr' => 0.0135,    // 1.35%
                'avg_cpc' => 4.20,      // R$ 4.20
                'avg_conversion_rate' => 0.0180, // 1.80%
                'confidence_score' => 0.70,
            ],
        ];

        foreach ($segments as $segment) {
            if (!isset($segment['technical_justification'])) {
                $segment['technical_justification'] = 'Temos observado alta concorrência digital para este segmento. Recomenda-se focar em criativos de alta qualidade e segmentação precisa para melhorar a performance.';
            }
            Segment::updateOrCreate(
                ['name' => $segment['name']],
                $segment
            );
        }
    }
}
