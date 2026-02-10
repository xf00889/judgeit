<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OverallRankingCategoryWeight extends Model
{
    use HasFactory;

    protected $fillable = [
        'overall_ranking_component_id',
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
     * Get the component this weight belongs to.
     */
    public function component(): BelongsTo
    {
        return $this->belongsTo(OverallRankingComponent::class, 'overall_ranking_component_id');
    }

    /**
     * Get the category for this weight.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
