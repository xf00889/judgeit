<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FixedScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'overall_ranking_component_id',
        'contestant_id',
        'score',
        'notes',
        'entered_by',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
        ];
    }

    /**
     * Get the component this fixed score belongs to.
     */
    public function component(): BelongsTo
    {
        return $this->belongsTo(OverallRankingComponent::class, 'overall_ranking_component_id');
    }

    /**
     * Get the contestant this fixed score is for.
     */
    public function contestant(): BelongsTo
    {
        return $this->belongsTo(Contestant::class);
    }

    /**
     * Get the user who entered this score.
     */
    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
    }
}
