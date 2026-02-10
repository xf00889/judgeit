<?php

namespace App\Repositories;

use App\Models\Contestant;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Collection;

interface ScoreRepositoryInterface
{
    /**
     * Save scores for a judge in a category.
     *
     * @param User $judge
     * @param Category $category
     * @param array $scores Array of score data
     * @return void
     */
    public function saveScores(User $judge, Category $category, array $scores): void;

    /**
     * Get all scores for a category.
     *
     * @param Category $category
     * @return Collection
     */
    public function getScoresForCategory(Category $category): Collection;

    /**
     * Get all scores for a contestant in a specific category.
     *
     * @param Contestant $contestant
     * @param Category $category
     * @return Collection
     */
    public function getScoresForContestant(Contestant $contestant, Category $category): Collection;

    /**
     * Check if a judge has submitted scores for a category.
     *
     * @param User $judge
     * @param Category $category
     * @return bool
     */
    public function hasJudgeSubmitted(User $judge, Category $category): bool;

    /**
     * Get the count of judges who have submitted scores for a category.
     *
     * @param Category $category
     * @return int
     */
    public function getJudgeSubmissionCount(Category $category): int;
}
