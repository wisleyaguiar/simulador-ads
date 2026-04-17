<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'total_population',
        'reachable_audience',
        'avg_cpm',
        'confidence_score',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_population' => 'integer',
            'reachable_audience' => 'integer',
            'avg_cpm' => 'decimal:2',
            'confidence_score' => 'decimal:2',
        ];
    }

    /**
     * Get the simulations for this region.
     */
    public function simulations(): HasMany
    {
        return $this->hasMany(Simulation::class);
    }
}
