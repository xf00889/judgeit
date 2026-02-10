<?php

namespace App\Actions;

use App\Models\Category;
use App\Models\User;
use App\Repositories\ContestantRepositoryInterface;
use App\Repositories\ScoreRepositoryInterface;
use Illuminate\Validation\ValidationException;

class ValidateScoreSubmissionAction
{
    public function __construct(
        private ScoreRepositoryInterface $scoreRepository,
        private ContestantRepositoryInterface $contestantRepository
    ) {}

    /**
     * Validate a score submission before saving.
     *
     * Checks:
     * - Judge is assigned to category
     * - Category is active
     * - Judge hasn't already submitted
     * - All contestants have scores
     * - All criteria have scores
     * - All scores within max_score bounds
     *
     * @param User $judge
     * @param Category $category
     * @param array $scores Array of score data with keys: contestant_id, criterion_id, score_value
     * @throws ValidationException
     */
    public function execute(User $judge, Category $category, array $scores): void
    {
        // Validate judge is assigned to category
        if (!$category->judges->contains($judge)) {
            throw ValidationException::withMessages([
                'judge' => ['You are not assigned to this category.']
            ]);
        }

        // Validate category is active
        if (!$category->isActive()) {
            throw ValidationException::withMessages([
                'category' => ['This category is not active for judging.']
            ]);
        }

        // Validate no duplicate submission
        if ($this->scoreRepository->hasJudgeSubmitted($judge, $category)) {
            throw ValidationException::withMessages([
                'submission' => ['You have already submitted scores for this category.']
            ]);
        }

        // Load category criteria
        $criteria = $category->criteria;
        
        // Get active contestants filtered by judge's gender preference
        $genderFilter = null;
        if ($judge->judging_gender && $judge->judging_gender !== 'both') {
            $genderFilter = $judge->judging_gender;
        }
        $activeContestants = $this->contestantRepository->getActiveContestants($genderFilter);

        // Validate all contestants and criteria have scores
        $expectedScoreCount = $criteria->count() * $activeContestants->count();
        if (count($scores) !== $expectedScoreCount) {
            throw ValidationException::withMessages([
                'scores' => [
                    "You must provide scores for all contestants and criteria. " .
                    "Expected {$expectedScoreCount} scores, received " . count($scores) . "."
                ]
            ]);
        }

        // Create a map of criterion_id => max_score for validation
        $criterionMaxScores = $criteria->pluck('max_score', 'id')->toArray();

        // Validate each score
        $contestantScores = [];
        $criterionScores = [];

        foreach ($scores as $index => $scoreData) {
            // Validate required fields
            if (!isset($scoreData['contestant_id']) || !isset($scoreData['criterion_id']) || !isset($scoreData['score_value'])) {
                throw ValidationException::withMessages([
                    "scores.{$index}" => ['Score data must include contestant_id, criterion_id, and score_value.']
                ]);
            }

            $contestantId = $scoreData['contestant_id'];
            $criterionId = $scoreData['criterion_id'];
            $scoreValue = $scoreData['score_value'];

            // Validate criterion belongs to this category
            if (!isset($criterionMaxScores[$criterionId])) {
                throw ValidationException::withMessages([
                    "scores.{$index}.criterion_id" => ['Invalid criterion for this category.']
                ]);
            }

            // Validate contestant is active
            if (!$activeContestants->contains('id', $contestantId)) {
                throw ValidationException::withMessages([
                    "scores.{$index}.contestant_id" => ['Invalid or inactive contestant.']
                ]);
            }

            // Validate score is within bounds
            $maxScore = $criterionMaxScores[$criterionId];
            if ($scoreValue < 0 || $scoreValue > $maxScore) {
                throw ValidationException::withMessages([
                    "scores.{$index}.score_value" => [
                        "Score must be between 0 and {$maxScore}."
                    ]
                ]);
            }

            // Track which contestants and criteria have been scored
            $contestantScores[$contestantId] = ($contestantScores[$contestantId] ?? 0) + 1;
            $criterionScores[$criterionId] = ($criterionScores[$criterionId] ?? 0) + 1;
        }

        // Verify all contestants have been scored for all criteria
        foreach ($activeContestants as $contestant) {
            if (!isset($contestantScores[$contestant->id]) || $contestantScores[$contestant->id] !== $criteria->count()) {
                throw ValidationException::withMessages([
                    'scores' => ["Missing scores for contestant {$contestant->contestant_number}."]
                ]);
            }
        }

        // Verify all criteria have been scored for all contestants
        foreach ($criteria as $criterion) {
            if (!isset($criterionScores[$criterion->id]) || $criterionScores[$criterion->id] !== $activeContestants->count()) {
                throw ValidationException::withMessages([
                    'scores' => ["Missing scores for criterion '{$criterion->name}'."]
                ]);
            }
        }
    }
}
