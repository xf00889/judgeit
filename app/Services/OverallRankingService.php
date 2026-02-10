<?php

namespace App\Services;

use App\Models\Contestant;
use App\Models\OverallRanking;
use App\Models\Category;
use App\Models\Score;
use Illuminate\Support\Collection;

class OverallRankingService
{
    /**
     * Calculate overall score for a contestant based on the active ranking formula.
     */
    public function calculateOverallScore(Contestant $contestant, OverallRanking $ranking): array
    {
        // Ensure all relationships are loaded
        if (!$ranking->relationLoaded('components')) {
            $ranking->load('components.category.criteria', 'components.categoryWeights.category.criteria');
        }

        $totalScore = 0;
        $breakdown = [];

        foreach ($ranking->components as $component) {
            $componentScore = 0;
            $componentBreakdown = [
                'name' => $component->name,
                'type' => $component->type,
                'percentage' => $component->percentage,
                'categories' => [],
            ];

            if ($component->type === 'fixed') {
                // For fixed type, use manually entered fixed scores
                $fixedScore = \App\Models\FixedScore::where('overall_ranking_component_id', $component->id)
                    ->where('contestant_id', $contestant->id)
                    ->first();
                
                if ($fixedScore) {
                    $componentScore = $fixedScore->score;
                    
                    $componentBreakdown['categories'][] = [
                        'name' => $component->name . ' (Fixed)',
                        'score' => $fixedScore->score,
                        'weight' => 100,
                        'notes' => $fixedScore->notes,
                    ];
                }
            } else {
                // For category_percentage type, calculate weighted contribution
                // Each category weight percentage is the direct contribution to overall score
                foreach ($component->categoryWeights as $weight) {
                    if ($weight->category) {
                        $categoryScore = $this->calculateCategoryScore($contestant, $weight->category);
                        // Direct contribution: category score (0-100) × weight percentage
                        $weightedScore = ($categoryScore * $weight->percentage) / 100;
                        $componentScore += $weightedScore;
                        
                        $componentBreakdown['categories'][] = [
                            'name' => $weight->category->name,
                            'score' => $categoryScore,
                            'weight' => $weight->percentage,
                            'weighted_score' => $weightedScore,
                        ];
                    }
                }
            }

            $componentBreakdown['component_score'] = round($componentScore, 2);
            
            // Both types: component_score is already the final contribution
            // - category_percentage: sum of (category_score × weight%)
            // - fixed: direct score entry (already represents final contribution)
            $contributionToTotal = $componentScore;
            
            $componentBreakdown['contribution'] = round($contributionToTotal, 2);
            
            $totalScore += $contributionToTotal;
            $breakdown[] = $componentBreakdown;
        }

        return [
            'total_score' => round($totalScore, 2),
            'breakdown' => $breakdown,
        ];
    }

    /**
     * Calculate a contestant's score for a specific category.
     */
    private function calculateCategoryScore(Contestant $contestant, ?Category $category): float
    {
        if (!$category) {
            return 0;
        }

        // Ensure criteria are loaded
        if (!$category->relationLoaded('criteria')) {
            $category->load('criteria');
        }
        
        $categoryScore = 0;
        $totalWeight = 0;

        foreach ($category->criteria as $criterion) {
            // Get all scores for this contestant and criterion
            $scores = Score::where('contestant_id', $contestant->id)
                ->where('criterion_id', $criterion->id)
                ->get();

            if ($scores->isEmpty()) {
                continue;
            }

            // Calculate average score for this criterion
            $averageScore = $scores->avg('score_value');
            
            // Sum the average scores directly (no weighting needed as max_score already represents the weight)
            $categoryScore += $averageScore;
        }

        return round($categoryScore, 2);
    }

    /**
     * Get all contestants with their overall scores.
     */
    public function getContestantsWithScores(OverallRanking $ranking): Collection
    {
        // Ensure all relationships are loaded upfront
        $ranking->load('components.category.criteria', 'components.categoryWeights.category.criteria');
        
        $contestants = Contestant::orderBy('contestant_number')->get();
        
        $results = $contestants->map(function ($contestant) use ($ranking) {
            $scoreData = $this->calculateOverallScore($contestant, $ranking);
            
            return [
                'contestant' => $contestant,
                'total_score' => $scoreData['total_score'],
                'breakdown' => $scoreData['breakdown'],
            ];
        });

        // Return results without ranking - let the controller handle gender-based ranking
        return $results;
    }
}
