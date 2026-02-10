<?php

namespace App\Repositories;

use App\Models\Contestant;
use App\Models\Score;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ScoreRepository implements ScoreRepositoryInterface
{
    /**
     * Save scores for a judge in a category.
     *
     * @param User $judge
     * @param Category $category
     * @param array $scores Array of score data
     * @return void
     */
    public function saveScores(User $judge, Category $category, array $scores): void
    {
        DB::transaction(function () use ($judge, $category, $scores) {
            // Delete any existing scores for this judge and category
            Score::where('judge_id', $judge->id)
                ->where('category_id', $category->id)
                ->delete();

            // Insert all new scores
            $timestamp = now();
            foreach ($scores as $scoreData) {
                Score::create([
                    'judge_id' => $judge->id,
                    'contestant_id' => $scoreData['contestant_id'],
                    'criterion_id' => $scoreData['criterion_id'],
                    'category_id' => $category->id,
                    'score_value' => $scoreData['score_value'],
                    'submitted_at' => $timestamp,
                ]);
            }
        });
    }

    /**
     * Get all scores for a category.
     *
     * @param Category $category
     * @return Collection
     */
    public function getScoresForCategory(Category $category): Collection
    {
        return Score::where('category_id', $category->id)
            ->with(['judge', 'contestant', 'criterion'])
            ->get();
    }

    /**
     * Get all scores for a contestant in a specific category.
     *
     * @param Contestant $contestant
     * @param Category $category
     * @return Collection
     */
    public function getScoresForContestant(Contestant $contestant, Category $category): Collection
    {
        return Score::where('contestant_id', $contestant->id)
            ->where('category_id', $category->id)
            ->with(['judge', 'criterion'])
            ->get();
    }

    /**
     * Check if a judge has submitted scores for a category.
     *
     * @param User $judge
     * @param Category $category
     * @return bool
     */
    public function hasJudgeSubmitted(User $judge, Category $category): bool
    {
        return Score::where('judge_id', $judge->id)
            ->where('category_id', $category->id)
            ->exists();
    }

    /**
     * Get the count of judges who have submitted scores for a category.
     *
     * @param Category $category
     * @return int
     */
    public function getJudgeSubmissionCount(Category $category): int
    {
        return Score::where('category_id', $category->id)
            ->distinct('judge_id')
            ->count('judge_id');
    }
}
