<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OverallRankingComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'overall_ranking_id',
        'name',
        'type',
        'category_id',
        'percentage',
    ];

    protected function casts(): array
    {
        return [
            'percentage' => 'decimal:2',
        ];
    }

    /**
     * Get the overall ranking this component belongs to.
     */
    public function overallRanking(): BelongsTo
    {
        return $this->belongsTo(OverallRanking::class);
    }

    /**
     * Get the category for this component.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the category weights for this component.
     */
    public function categoryWeights(): HasMany
    {
        return $this->hasMany(OverallRankingCategoryWeight::class);
    }

    /**
     * Get the fixed scores for this component.
     */
    public function fixedScores(): HasMany
    {
        return $this->hasMany(FixedScore::class);
    }
}
