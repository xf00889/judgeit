<?php

namespace App\Actions;

use App\Models\Contestant;
use App\Services\ResultServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Select top 3 finalists based on preliminary scores.
 * Supports ties - if multiple contestants tie for 3rd place, all are selected.
 */
class SelectFinalistsAction
{
    public function __construct(
        private ResultServiceInterface $resultService
    ) {}

    /**
     * Execute finalist selection.
     * 
     * @return array ['finalists' => Collection, 'count' => int, 'has_ties' => bool]
     */
    public function execute(): array
    {
        // Generate current rankings
        $rankings = $this->resultService->generateRankings();

        if ($rankings->isEmpty()) {
            throw new \Exception('No contestants have scores yet. Cannot select finalists.');
        }

        // Get top 3 unique scores
        $topScores = $rankings->pluck('overallScore')
            ->unique()
            ->sortDesc()
            ->take(3)
            ->values();

        if ($topScores->count() < 3) {
            Log::warning('Less than 3 unique scores available for finalist selection', [
                'unique_scores' => $topScores->count(),
            ]);
        }

        // Get the 3rd place score (or lowest available)
        $cutoffScore = $topScores->last();

        // Select all contestants with scores >= cutoff (handles ties)
        $finalists = $rankings->filter(function ($ranking) use ($cutoffScore) {
            return $ranking->overallScore >= $cutoffScore;
        });

        // Check if there are ties
        $hasTies = $finalists->count() > 3;

        // Update database
        DB::transaction(function () use ($finalists) {
            // Reset all contestants
            Contestant::query()->update([
                'is_finalist' => false,
                'preliminary_rank' => null,
                'preliminary_score' => null,
            ]);

            // Mark finalists
            foreach ($finalists as $index => $ranking) {
                Contestant::where('id', $ranking->contestant->id)->update([
                    'is_finalist' => true,
                    'preliminary_rank' => $ranking->rank,
                    'preliminary_score' => $ranking->overallScore,
                ]);
            }
        });

        Log::info('Finalists selected', [
            'count' => $finalists->count(),
            'has_ties' => $hasTies,
            'cutoff_score' => $cutoffScore,
            'finalist_ids' => $finalists->pluck('contestant.id')->toArray(),
        ]);

        return [
            'finalists' => $finalists,
            'count' => $finalists->count(),
            'has_ties' => $hasTies,
            'cutoff_score' => $cutoffScore,
        ];
    }

    /**
     * Check if finalists can be selected.
     * 
     * @return array ['can_select' => bool, 'reason' => string|null]
     */
    public function canSelectFinalists(): array
    {
        // Get all active preliminary categories
        $preliminaryCategories = \App\Models\Category::where('is_finals', false)
            ->where('status', 'active')
            ->with('judges')
            ->get();

        if ($preliminaryCategories->isEmpty()) {
            return [
                'can_select' => false,
                'reason' => 'No preliminary categories exist.',
            ];
        }

        // Check if all judges have submitted scores for all their assigned preliminary categories
        $incompleteCategories = [];
        
        foreach ($preliminaryCategories as $category) {
            $judges = $category->judges;
            
            if ($judges->isEmpty()) {
                $incompleteCategories[] = $category->name . ' (no judges assigned)';
                continue;
            }

            foreach ($judges as $judge) {
                // Check if this judge has submitted scores for this category
                $hasSubmitted = \App\Models\Score::where('judge_id', $judge->id)
                    ->where('category_id', $category->id)
                    ->exists();
                
                if (!$hasSubmitted) {
                    $incompleteCategories[] = $category->name . ' (Judge: ' . $judge->name . ')';
                }
            }
        }

        if (!empty($incompleteCategories)) {
            $categoryList = implode(', ', array_unique($incompleteCategories));
            return [
                'can_select' => false,
                'reason' => 'Not all judges have completed their scoresheets. Incomplete: ' . $categoryList,
            ];
        }

        // Check if there are enough contestants with scores
        $rankings = $this->resultService->generateRankings();
        
        if ($rankings->isEmpty()) {
            return [
                'can_select' => false,
                'reason' => 'No contestants have scores yet.',
            ];
        }

        if ($rankings->count() < 3) {
            return [
                'can_select' => true,
                'reason' => "Only {$rankings->count()} contestant(s) have scores. All will be selected as finalists.",
            ];
        }

        return [
            'can_select' => true,
            'reason' => 'All judges have completed their scoresheets. Ready to finalize.',
        ];
    }
}
