<?php

namespace App\Actions;

use App\Models\Contestant;
use Illuminate\Support\Collection;

class CalculateOverallScoreAction
{
    public function __construct(
        private CalculateCategoryScoreAction $calculateCategoryScoreAction
    ) {}

    /**
     * Calculate a contestant's overall score across all finalized categories.
     *
     * @param Contestant $contestant
     * @param Collection $finalizedCategories Collection of finalized Category models
     * @return float Overall score (sum of all category scores)
     */
    public function execute(Contestant $contestant, Collection $finalizedCategories): float
    {
        // If no finalized categories, return 0
        if ($finalizedCategories->isEmpty()) {
            return 0.0;
        }

        $totalScore = 0.0;

        foreach ($finalizedCategories as $category) {
            // Ensure criteria are loaded
            if (!$category->relationLoaded('criteria')) {
                $category->load('criteria');
            }

            // Get all scores for this contestant in this category
            $scores = $contestant->scores()
                ->where('category_id', $category->id)
                ->get();

            // Calculate category score
            $categoryScore = $this->calculateCategoryScoreAction->execute(
                $contestant,
                $category,
                $scores
            );

            $totalScore += $categoryScore;
        }

        return round($totalScore, 2);
    }
}
