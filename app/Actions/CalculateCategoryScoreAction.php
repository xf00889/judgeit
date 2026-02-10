<?php

namespace App\Actions;

use App\Models\Contestant;
use App\Models\Category;
use Illuminate\Support\Collection;

class CalculateCategoryScoreAction
{
    /**
     * Calculate a contestant's final score for a category.
     *
     * Algorithm:
     * 1. For each criterion in the category:
     *    - Get all scores from all judges for this criterion
     *    - Calculate average score across judges
     * 2. Sum all average criterion scores
     * 3. Return final category score
     *
     * @param Contestant $contestant
     * @param Category $category
     * @param Collection $scores Collection of Score models for this contestant and category
     * @return float Category score (0-100 scale)
     */
    public function execute(Contestant $contestant, Category $category, Collection $scores): float
    {
        // If no scores, return 0
        if ($scores->isEmpty()) {
            return 0.0;
        }

        // Ensure criteria are loaded
        if (!$category->relationLoaded('criteria')) {
            $category->load('criteria');
        }

        // If no criteria, return 0
        if ($category->criteria->isEmpty()) {
            return 0.0;
        }

        $categoryScore = 0.0;

        foreach ($category->criteria as $criterion) {
            // Get all scores for this criterion from all judges
            $criterionScores = $scores->where('criterion_id', $criterion->id);

            // If no scores for this criterion, skip it
            if ($criterionScores->isEmpty()) {
                continue;
            }

            // Calculate average score across all judges for this criterion
            $averageScore = $criterionScores->avg('score_value');

            // Sum the average scores directly (no percentage weighting needed)
            $categoryScore += $averageScore;
        }

        return round($categoryScore, 2);
    }
}
