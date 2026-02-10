<?php

namespace App\Services;

use App\Actions\SaveScoresAction;
use App\Actions\ValidateScoreSubmissionAction;
use App\Events\ScoresSubmittedEvent;
use App\Models\Category;
use App\Models\User;
use App\Repositories\ContestantRepositoryInterface;
use App\Repositories\ScoreRepositoryInterface;
use App\Repositories\CategoryRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScoringService implements ScoringServiceInterface
{
    public function __construct(
        private ScoreRepositoryInterface $scoreRepository,
        private CategoryRepositoryInterface $categoryRepository,
        private ContestantRepositoryInterface $contestantRepository,
        private ValidateScoreSubmissionAction $validateAction,
        private SaveScoresAction $saveAction
    ) {}

    /**
     * Submit scores for a judge in a category.
     *
     * @param User $judge
     * @param Category $category
     * @param array $scores Array of score data with keys: contestant_id, criterion_id, score_value
     * @return void
     * @throws \Exception
     */
    public function submitScores(User $judge, Category $category, array $scores): void
    {
        // Validate category is active
        if (!$category->isActive()) {
            throw new \Exception(
                "Cannot submit scores. Category '{$category->name}' is not active."
            );
        }

        // Check for duplicate submission
        if ($this->scoreRepository->hasJudgeSubmitted($judge, $category)) {
            throw new \Exception(
                "You have already submitted scores for this category."
            );
        }

        // Validate judge is assigned to category
        if (!$category->judges->contains($judge)) {
            throw new \Exception(
                "You are not assigned to this category."
            );
        }

        // Validate scores
        $this->validateAction->execute($judge, $category, $scores);

        // Save with transaction
        try {
            DB::transaction(function () use ($judge, $category, $scores) {
                $this->saveAction->execute($judge, $category, $scores);
            });
        } catch (\Exception $e) {
            Log::error('Score submission failed', [
                'judge_id' => $judge->id,
                'category_id' => $category->id,
                'exception' => $e->getMessage(),
            ]);
            throw new \Exception('Failed to save scores. Please try again.');
        }

        // Broadcast event (non-blocking, failures logged but don't fail submission)
        try {
            broadcast(new ScoresSubmittedEvent($category, $judge));
        } catch (\Exception $e) {
            Log::warning('Failed to broadcast score submission event', [
                'category_id' => $category->id,
                'judge_id' => $judge->id,
                'exception' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Check if a judge has already submitted scores for a category.
     *
     * @param User $judge
     * @param Category $category
     * @return bool
     */
    public function hasJudgeSubmitted(User $judge, Category $category): bool
    {
        return $this->scoreRepository->hasJudgeSubmitted($judge, $category);
    }

    /**
     * Get the judging form data for a judge and category.
     *
     * @param User $judge
     * @param Category $category
     * @return array
     */
    public function getJudgingForm(User $judge, Category $category): array
    {
        // Load category with relationships
        $category->load(['criteria', 'judges']);

        // Verify judge is assigned to category
        if (!$category->judges->contains($judge)) {
            throw new \Exception("You are not assigned to this category.");
        }

        // Determine gender filter based on judge's judging_gender setting
        $genderFilter = null;
        if ($judge->judging_gender && $judge->judging_gender !== 'both') {
            $genderFilter = $judge->judging_gender;
        }

        // Get contestants based on category type
        if ($category->isFinals()) {
            // For finals, only show finalists
            $contestants = $this->contestantRepository->getContestantsForFinalsJudging($genderFilter);
        } else {
            // For preliminary rounds, show all active contestants
            $contestants = $this->contestantRepository->getActiveContestants($genderFilter);
        }

        // Get criteria for this category
        $criteria = $category->criteria;

        return [
            'category' => $category,
            'contestants' => $contestants,
            'criteria' => $criteria,
            'has_submitted' => $this->hasJudgeSubmitted($judge, $category),
        ];
    }
}
