<?php

namespace App\Services;

use App\Actions\CalculateOverallScoreAction;
use App\Actions\CalculateCategoryScoreAction;
use App\Actions\GenerateRankingsAction;
use App\Models\Contestant;
use App\Models\Category;
use App\Repositories\ScoreRepositoryInterface;
use App\Repositories\CategoryRepositoryInterface;
use Illuminate\Support\Collection;

class ResultService implements ResultServiceInterface
{
    public function __construct(
        private ScoreRepositoryInterface $scoreRepository,
        private CategoryRepositoryInterface $categoryRepository,
        private CalculateCategoryScoreAction $calculateCategoryAction,
        private CalculateOverallScoreAction $calculateOverallAction,
        private GenerateRankingsAction $generateRankingsAction
    ) {}

    /**
     * Calculate a contestant's score for a specific category.
     *
     * @param Contestant $contestant
     * @param Category $category
     * @return float
     */
    public function calculateCategoryScore(Contestant $contestant, Category $category): float
    {
        // Get all scores for this contestant in this category
        $scores = $this->scoreRepository->getScoresForContestant($contestant, $category);

        // Use action to calculate category score
        return $this->calculateCategoryAction->execute($contestant, $category, $scores);
    }

    /**
     * Calculate a contestant's overall score across all finalized categories.
     *
     * @param Contestant $contestant
     * @return float
     */
    public function calculateOverallScore(Contestant $contestant): float
    {
        // Get all finalized categories
        $finalizedCategories = $this->categoryRepository->getFinalizedCategories();

        // Use action to calculate overall score
        return $this->calculateOverallAction->execute($contestant, $finalizedCategories);
    }

    /**
     * Generate rankings for all contestants based on overall scores.
     *
     * @return Collection Collection of RankingResultDTO
     */
    public function generateRankings(): Collection
    {
        // Use action to generate rankings
        return $this->generateRankingsAction->execute();
    }

    /**
     * Get judging progress for a category.
     *
     * @param Category $category
     * @return array
     */
    public function getJudgingProgress(Category $category): array
    {
        // Load judges relationship
        $category->load('judges');

        // Get total number of assigned judges
        $totalJudges = $category->judges->count();

        // Get number of judges who have submitted
        $submittedCount = $this->scoreRepository->getJudgeSubmissionCount($category);

        // Calculate percentage
        $percentage = $totalJudges > 0 
            ? round(($submittedCount / $totalJudges) * 100, 2)
            : 0.0;

        return [
            'category_id' => $category->id,
            'category_name' => $category->name,
            'total_judges' => $totalJudges,
            'submitted_count' => $submittedCount,
            'percentage' => $percentage,
        ];
    }
}
