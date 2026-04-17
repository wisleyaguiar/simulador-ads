<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Seed the regions table with Brazilian states data.
     * Population data based on IBGE estimates.
     * Reachable audience: ~75% of total population.
     * Confidence score default: 0.70 (estimated data).
     */
    public function run(): void
    {
        $regions = [
            ['name' => 'São Paulo',            'total_population' => 46649132, 'reachable_audience' => 34987000, 'avg_cpm' => 25.00],
            ['name' => 'Rio de Janeiro',        'total_population' => 17503000, 'reachable_audience' => 13127000, 'avg_cpm' => 28.00],
            ['name' => 'Minas Gerais',          'total_population' => 21412000, 'reachable_audience' => 16059000, 'avg_cpm' => 20.00],
            ['name' => 'Bahia',                 'total_population' => 14985000, 'reachable_audience' => 11239000, 'avg_cpm' => 15.00],
            ['name' => 'Paraná',                'total_population' => 11597000, 'reachable_audience' => 8698000,  'avg_cpm' => 22.00],
            ['name' => 'Rio Grande do Sul',     'total_population' => 11467000, 'reachable_audience' => 8600000,  'avg_cpm' => 22.00],
            ['name' => 'Pernambuco',            'total_population' => 9674000,  'reachable_audience' => 7256000,  'avg_cpm' => 16.00],
            ['name' => 'Ceará',                 'total_population' => 9240000,  'reachable_audience' => 6930000,  'avg_cpm' => 14.00],
            ['name' => 'Pará',                  'total_population' => 8777000,  'reachable_audience' => 6583000,  'avg_cpm' => 12.00],
            ['name' => 'Santa Catarina',        'total_population' => 7610000,  'reachable_audience' => 5708000,  'avg_cpm' => 24.00],
            ['name' => 'Maranhão',              'total_population' => 7154000,  'reachable_audience' => 5366000,  'avg_cpm' => 11.00],
            ['name' => 'Goiás',                 'total_population' => 7206000,  'reachable_audience' => 5405000,  'avg_cpm' => 18.00],
            ['name' => 'Amazonas',              'total_population' => 4269000,  'reachable_audience' => 3202000,  'avg_cpm' => 13.00],
            ['name' => 'Espírito Santo',        'total_population' => 4109000,  'reachable_audience' => 3082000,  'avg_cpm' => 21.00],
            ['name' => 'Paraíba',               'total_population' => 4059000,  'reachable_audience' => 3044000,  'avg_cpm' => 13.00],
            ['name' => 'Rio Grande do Norte',   'total_population' => 3560000,  'reachable_audience' => 2670000,  'avg_cpm' => 14.00],
            ['name' => 'Mato Grosso',           'total_population' => 3567000,  'reachable_audience' => 2675000,  'avg_cpm' => 17.00],
            ['name' => 'Alagoas',               'total_population' => 3365000,  'reachable_audience' => 2524000,  'avg_cpm' => 12.00],
            ['name' => 'Piauí',                 'total_population' => 3289000,  'reachable_audience' => 2467000,  'avg_cpm' => 11.00],
            ['name' => 'Distrito Federal',      'total_population' => 3094000,  'reachable_audience' => 2321000,  'avg_cpm' => 30.00],
            ['name' => 'Mato Grosso do Sul',    'total_population' => 2839000,  'reachable_audience' => 2129000,  'avg_cpm' => 18.00],
            ['name' => 'Sergipe',               'total_population' => 2339000,  'reachable_audience' => 1754000,  'avg_cpm' => 13.00],
            ['name' => 'Rondônia',              'total_population' => 1815000,  'reachable_audience' => 1361000,  'avg_cpm' => 14.00],
            ['name' => 'Tocantins',             'total_population' => 1607000,  'reachable_audience' => 1205000,  'avg_cpm' => 13.00],
            ['name' => 'Acre',                  'total_population' => 906000,   'reachable_audience' => 680000,   'avg_cpm' => 12.00],
            ['name' => 'Amapá',                 'total_population' => 877000,   'reachable_audience' => 658000,   'avg_cpm' => 12.00],
            ['name' => 'Roraima',               'total_population' => 652000,   'reachable_audience' => 489000,   'avg_cpm' => 12.00],
        ];

        foreach ($regions as $region) {
            Region::updateOrCreate(
                ['name' => $region['name']],
                array_merge($region, ['confidence_score' => 0.70])
            );
        }
    }
}
