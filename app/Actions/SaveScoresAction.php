<?php

namespace App\Actions;

use App\Models\Score;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SaveScoresAction
{
    /**
     * Save all scores in a database transaction.
     *
     * @param User $judge
     * @param Category $category
     * @param array $scores Array of score data with keys: contestant_id, criterion_id, score_value
     * @throws \Exception
     */
    public function execute(User $judge, Category $category, array $scores): void
    {
        DB::transaction(function () use ($judge, $category, $scores) {
            // Delete any existing scores for this judge and category
            // (shouldn't exist due to validation, but ensures clean state)
            Score::where('judge_id', $judge->id)
                ->where('category_id', $category->id)
                ->delete();

            // Record submission timestamp
            $submittedAt = now();

            // Insert all new scores
            foreach ($scores as $scoreData) {
                Score::create([
                    'judge_id' => $judge->id,
                    'contestant_id' => $scoreData['contestant_id'],
                    'criterion_id' => $scoreData['criterion_id'],
                    'category_id' => $category->id,
                    'score_value' => $scoreData['score_value'],
                    'submitted_at' => $submittedAt,
                ]);
            }
        });
    }
}
