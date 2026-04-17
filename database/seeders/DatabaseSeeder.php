<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed benchmark data
        $this->call([
            SegmentSeeder::class,
            RegionSeeder::class,
        ]);

        // Create test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'user',
        ]);

        // Create admin user
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@simuladorads.com',
            'role' => 'admin',
        ]);
    }
}
