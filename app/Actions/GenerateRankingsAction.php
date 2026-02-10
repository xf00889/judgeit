<?php

namespace App\Actions;

use App\DTOs\RankingResultDTO;
use App\Models\Contestant;
use App\Repositories\CategoryRepositoryInterface;
use Illuminate\Support\Collection;

class GenerateRankingsAction
{
    public function __construct(
        private CalculateOverallScoreAction $calculateOverallScoreAction,
        private CalculateCategoryScoreAction $calculateCategoryScoreAction,
        private CategoryRepositoryInterface $categoryRepository
    ) {}

    /**
     * Generate rankings for all contestants based on overall scores.
     *
     * @return Collection<RankingResultDTO>
     */
    public function execute(): Collection
    {
        // Get all finalized categories
        $finalizedCategories = $this->categoryRepository->getFinalizedCategories();

        // Get all contestants
        $contestants = Contestant::all();

        // Calculate overall scores for all contestants
        $contestantScores = [];

        foreach ($contestants as $contestant) {
            $overallScore = $this->calculateOverallScoreAction->execute(
                $contestant,
                $finalizedCategories
            );

            // Calculate category scores for this contestant
            $categoryScores = [];
            foreach ($finalizedCategories as $category) {
                $scores = $contestant->scores()
                    ->where('category_id', $category->id)
                    ->get();

                $categoryScore = $this->calculateCategoryScoreAction->execute(
                    $contestant,
                    $category,
                    $scores
                );

                $categoryScores[$category->name] = $categoryScore;
            }

            $contestantScores[] = [
                'contestant' => $contestant,
                'overall_score' => $overallScore,
                'category_scores' => $categoryScores,
            ];
        }

        // Sort by overall score descending
        usort($contestantScores, function ($a, $b) {
            return $b['overall_score'] <=> $a['overall_score'];
        });

        // Assign ranks (handle ties with same rank)
        $rankings = collect();
        $currentRank = 1;
        $previousScore = null;

        foreach ($contestantScores as $index => $data) {
            if ($previousScore !== null && $data['overall_score'] < $previousScore) {
                $currentRank = $index + 1;
            }

            $rankings->push(new RankingResultDTO(
                rank: $currentRank,
                contestant: $data['contestant'],
                overallScore: $data['overall_score'],
                categoryScores: $data['category_scores']
            ));

            $previousScore = $data['overall_score'];
        }

        return $rankings;
    }
}
