<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Simulation extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     * Only created_at is used (no updated_at).
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'budget',
        'payment_type',
        'campaign_days',
        'region_id',
        'segment_id',
        'goal',
        'maturity_level',
        'campaign_month',
        'results_json',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'budget' => 'decimal:2',
            'campaign_days' => 'integer',
            'campaign_month' => 'integer',
            'results_json' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the simulation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the segment used in this simulation.
     */
    public function segment(): BelongsTo
    {
        return $this->belongsTo(Segment::class);
    }

    /**
     * Get the region used in this simulation.
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }
}
