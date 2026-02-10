<?php

namespace App\Services;

use App\Models\Category;
use App\Models\User;

interface ScoringServiceInterface
{
    /**
     * Submit scores for a judge in a category.
     *
     * @param User $judge
     * @param Category $category
     * @param array $scores Array of score data with keys: contestant_id, criterion_id, score_value
     * @return void
     * @throws \Exception
     */
    public function submitScores(User $judge, Category $category, array $scores): void;

    /**
     * Check if a judge has already submitted scores for a category.
     *
     * @param User $judge
     * @param Category $category
     * @return bool
     */
    public function hasJudgeSubmitted(User $judge, Category $category): bool;

    /**
     * Get the judging form data for a judge and category.
     *
     * @param User $judge
     * @param Category $category
     * @return array
     */
    public function getJudgingForm(User $judge, Category $category): array;
}
