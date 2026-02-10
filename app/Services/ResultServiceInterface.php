<?php

namespace App\Services;

use App\Models\Contestant;
use App\Models\Category;
use Illuminate\Support\Collection;

interface ResultServiceInterface
{
    /**
     * Calculate a contestant's score for a specific category.
     *
     * @param Contestant $contestant
     * @param Category $category
     * @return float
     */
    public function calculateCategoryScore(Contestant $contestant, Category $category): float;

    /**
     * Calculate a contestant's overall score across all finalized categories.
     *
     * @param Contestant $contestant
     * @return float
     */
    public function calculateOverallScore(Contestant $contestant): float;

    /**
     * Generate rankings for all contestants based on overall scores.
     *
     * @return Collection Collection of RankingResultDTO
     */
    public function generateRankings(): Collection;

    /**
     * Get judging progress for a category.
     *
     * @param Category $category
     * @return array
     */
    public function getJudgingProgress(Category $category): array;
}
