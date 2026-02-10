<?php

namespace App\Http\Controllers\Tabulator;

use App\Actions\SelectFinalistsAction;
use App\Http\Controllers\Controller;
use App\Repositories\ContestantRepositoryInterface;
use App\Services\ResultServiceInterface;
use App\Services\OverallRankingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FinalistController extends Controller
{
    public function __construct(
        private SelectFinalistsAction $selectFinalistsAction,
        private ContestantRepositoryInterface $contestantRepository,
        private ResultServiceInterface $resultService,
        private OverallRankingService $overallRankingService
    ) {}

    /**
     * Display finalist selection page.
     */
    public function index(): View
    {
        // Check if finalists can be selected
        $selectionStatus = $this->selectFinalistsAction->canSelectFinalists();

        // Get current finalists if any
        $finalists = $this->contestantRepository->getFinalists();

        // Get the active overall ranking
        $activeRanking = \App\Models\OverallRanking::where('is_active', true)
            ->with('components.category.criteria', 'components.categoryWeights.category.criteria')
            ->first();

        if ($activeRanking) {
            // Use modern overall ranking system
            $results = $this->overallRankingService->getContestantsWithScores($activeRanking);
            
            // Sort by total score descending and assign temporary ranks
            $sortedResults = $results->sortByDesc('total_score')->values();
            
            // Convert to RankingResultDTO format for view compatibility
            $rankings = $sortedResults->map(function ($result, $index) {
                // Extract category scores from breakdown
                $categoryScores = [];
                foreach ($result['breakdown'] as $component) {
                    foreach ($component['categories'] as $category) {
                        $categoryScores[$category['name']] = $category['score'];
                    }
                }
                
                return new \App\DTOs\RankingResultDTO(
                    rank: $index + 1, // Temporary rank, will be re-ranked by gender
                    contestant: $result['contestant'],
                    overallScore: $result['total_score'],
                    categoryScores: $categoryScores
                );
            });
        } else {
            // Fallback to legacy system
            $rankings = $this->resultService->generateRankings();
        }

        // Group rankings by gender
        $rankingsByGender = $rankings->groupBy(function ($ranking) {
            return $ranking->contestant->gender ?? 'Not Specified';
        });

        // Re-rank within each gender group
        foreach ($rankingsByGender as $gender => $genderRankings) {
            $rankingsByGender[$gender] = $this->assignRanksToCollection($genderRankings);
        }

        // Identify top 3 for each gender
        $topThreeByGender = [];
        foreach ($rankingsByGender as $gender => $genderRankings) {
            $topThreeByGender[$gender] = [
                'first' => $genderRankings->where('rank', 1)->first(),
                'second' => $genderRankings->where('rank', 2)->first(),
                'third' => $genderRankings->where('rank', 3)->first(),
            ];
        }

        return view('tabulator.finalists.index', [
            'can_select' => $selectionStatus['can_select'],
            'selection_reason' => $selectionStatus['reason'],
            'finalists' => $finalists,
            'rankings' => $rankings,
            'rankingsByGender' => $rankingsByGender,
            'topThreeByGender' => $topThreeByGender,
            'has_finalists' => $finalists->isNotEmpty(),
        ]);
    }

    /**
     * Assign ranks to a collection of ranking DTOs.
     */
    private function assignRanksToCollection($rankings)
    {
        // Sort by overall score descending
        $sorted = $rankings->sortByDesc('overallScore')->values();
        
        $currentRank = 1;
        $previousScore = null;
        $sameRankCount = 0;
        
        return $sorted->map(function ($ranking, $index) use (&$currentRank, &$previousScore, &$sameRankCount) {
            $score = $ranking->overallScore;
            
            // Use a much smaller tolerance for tie detection (0.001 instead of 0.01)
            if ($previousScore !== null && abs($score - $previousScore) < 0.001) {
                // Same score as previous - it's a tie
                $newRank = $currentRank;
                $sameRankCount++;
            } else {
                // Different score - update rank
                if ($sameRankCount > 0) {
                    $currentRank += $sameRankCount + 1;
                    $sameRankCount = 0;
                } else if ($index > 0) {
                    $currentRank++;
                }
                $newRank = $currentRank;
            }
            
            $previousScore = $score;
            
            // Create new DTO with updated rank
            return new \App\DTOs\RankingResultDTO(
                rank: $newRank,
                contestant: $ranking->contestant,
                overallScore: $ranking->overallScore,
                categoryScores: $ranking->categoryScores
            );
        });
    }

    /**
     * Finalize top 3 per gender - mark top 3 as finalists and others as inactive.
     */
    public function finalize(): RedirectResponse
    {
        try {
            // Check if selection is allowed
            $selectionStatus = $this->selectFinalistsAction->canSelectFinalists();
            
            if (!$selectionStatus['can_select']) {
                return redirect()
                    ->route('tabulator.finalists.index')
                    ->with('error', $selectionStatus['reason']);
            }

            // Get the active overall ranking
            $activeRanking = \App\Models\OverallRanking::where('is_active', true)
                ->with('components.category.criteria', 'components.categoryWeights.category.criteria')
                ->first();

            if (!$activeRanking) {
                return redirect()
                    ->route('tabulator.finalists.index')
                    ->with('error', 'No active overall ranking found. Please activate an overall ranking first.');
            }

            // Get rankings
            $results = $this->overallRankingService->getContestantsWithScores($activeRanking);
            
            // Sort by total score descending and assign temporary ranks
            $sortedResults = $results->sortByDesc('total_score')->values();
            
            $rankings = $sortedResults->map(function ($result, $index) {
                $categoryScores = [];
                foreach ($result['breakdown'] as $component) {
                    foreach ($component['categories'] as $category) {
                        $categoryScores[$category['name']] = $category['score'];
                    }
                }
                
                return new \App\DTOs\RankingResultDTO(
                    rank: $index + 1, // Temporary rank, will be re-ranked by gender
                    contestant: $result['contestant'],
                    overallScore: $result['total_score'],
                    categoryScores: $categoryScores
                );
            });

            // Group by gender and re-rank
            $rankingsByGender = $rankings->groupBy(function ($ranking) {
                return $ranking->contestant->gender ?? 'Not Specified';
            });

            foreach ($rankingsByGender as $gender => $genderRankings) {
                $rankingsByGender[$gender] = $this->assignRanksToCollection($genderRankings);
            }

            // First, mark all contestants as inactive and not finalists
            \App\Models\Contestant::query()->update([
                'is_active' => false,
                'is_finalist' => false,
                'preliminary_rank' => null,
                'preliminary_score' => null,
            ]);

            $finalistCount = 0;
            $genderCounts = [];

            // Mark top 3 per gender as finalists and active
            foreach ($rankingsByGender as $gender => $genderRankings) {
                $top3 = $genderRankings->filter(fn($r) => $r->rank <= 3);
                
                foreach ($top3 as $ranking) {
                    \App\Models\Contestant::where('id', $ranking->contestant->id)
                        ->update([
                            'is_active' => true,
                            'is_finalist' => true,
                            'preliminary_rank' => $ranking->rank,
                            'preliminary_score' => $ranking->overallScore,
                        ]);
                    
                    $finalistCount++;
                }
                
                $genderCounts[$gender] = $top3->count();
            }

            $message = "Successfully finalized {$finalistCount} finalist(s). ";
            foreach ($genderCounts as $gender => $count) {
                $message .= "{$gender}: {$count} finalists. ";
            }
            $message .= "Other contestants have been marked as inactive.";

            return redirect()
                ->route('tabulator.finalists.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Finalize finalists error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()
                ->route('tabulator.finalists.index')
                ->with('error', 'Failed to finalize finalists: ' . $e->getMessage());
        }
    }

    /**
     * Execute finalist selection.
     */
    public function select(): RedirectResponse
    {
        try {
            // Check if selection is allowed
            $selectionStatus = $this->selectFinalistsAction->canSelectFinalists();
            
            if (!$selectionStatus['can_select']) {
                return redirect()
                    ->route('tabulator.finalists.index')
                    ->with('error', $selectionStatus['reason']);
            }

            // Execute selection
            $result = $this->selectFinalistsAction->execute();

            $message = "Successfully selected {$result['count']} finalist(s).";
            
            if ($result['has_ties']) {
                $message .= " Note: There are ties, so more than 3 contestants were selected.";
            }

            return redirect()
                ->route('tabulator.finalists.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()
                ->route('tabulator.finalists.index')
                ->with('error', 'Failed to select finalists: ' . $e->getMessage());
        }
    }

    /**
     * Display print view for finalists.
     */
    public function print(): \Illuminate\View\View
    {
        // Get current finalists grouped by gender
        $finalists = $this->contestantRepository->getFinalists();
        
        $finalistsByGender = $finalists->groupBy(function ($finalist) {
            return $finalist->gender ?? 'Not Specified';
        });

        // Sort finalists within each gender by preliminary rank
        foreach ($finalistsByGender as $gender => $genderFinalists) {
            $finalistsByGender[$gender] = $genderFinalists->sortBy('preliminary_rank');
        }

        return view('tabulator.finalists.print', [
            'finalistsByGender' => $finalistsByGender,
        ]);
    }

    /**
     * Reset finalist selection.
     */
    public function reset(): RedirectResponse
    {
        try {
            \App\Models\Contestant::query()->update([
                'is_finalist' => false,
                'preliminary_rank' => null,
                'preliminary_score' => null,
            ]);

            return redirect()
                ->route('tabulator.finalists.index')
                ->with('success', 'Finalist selection has been reset.');

        } catch (\Exception $e) {
            return redirect()
                ->route('tabulator.finalists.index')
                ->with('error', 'Failed to reset finalists: ' . $e->getMessage());
        }
    }
}
