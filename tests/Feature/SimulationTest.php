<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Region;
use App\Models\Segment;

class SimulationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_save_and_retrieve_simulation()
    {
        $user = User::factory()->create();
        $region = Region::create(['name' => 'Test Region', 'avg_cpm' => 15.00, 'total_population' => 1000000, 'reachable_audience' => 500000]);
        $segment = Segment::create(['name' => 'Test Segment', 'avg_cpc' => 1.50, 'avg_ctr' => 2.0, 'avg_conversion_rate' => 5.0, 'base_ticket' => 100.00]);

        $payload = [
            'budget' => 5000,
            'payment_type' => 'Boleto',
            'campaign_days' => 30,
            'region_id' => $region->id,
            'segment_id' => $segment->id,
            'goal' => 'Leads',
            'maturity_level' => 'Iniciante',
            'campaign_month' => 4,
            'deduct_tax' => true,
        ];

        // 1. Post to save simulation
        $response = $this->actingAs($user)->postJson('/api/simulations', $payload);

        $response->assertStatus(201)
                 ->assertJsonPath('budget', '5000.00')
                 ->assertJsonPath('maturity_level', 'Iniciante');

        // 2. Fetch history
        $historyResponse = $this->actingAs($user)->getJson('/api/simulations');

        $historyResponse->assertStatus(200);
        $this->assertCount(1, $historyResponse->json());
        $this->assertEquals('5000.00', $historyResponse->json()[0]['budget']);
    }
}
