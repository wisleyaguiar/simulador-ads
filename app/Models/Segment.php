<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Segment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'avg_ctr',
        'avg_cpc',
        'avg_conversion_rate',
        'confidence_score',
        'technical_justification',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'avg_ctr' => 'decimal:4',
            'avg_cpc' => 'decimal:2',
            'avg_conversion_rate' => 'decimal:4',
            'confidence_score' => 'decimal:2',
        ];
    }

    /**
     * Get the simulations for this segment.
     */
    public function simulations(): HasMany
    {
        return $this->hasMany(Simulation::class);
    }
}
