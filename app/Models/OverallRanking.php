<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OverallRanking extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the components for this overall ranking.
     */
    public function components(): HasMany
    {
        return $this->hasMany(OverallRankingComponent::class);
    }

    /**
     * Activate this overall ranking and deactivate others.
     */
    public function activate(): void
    {
        // Deactivate all other overall rankings
        self::where('id', '!=', $this->id)->update(['is_active' => false]);
        
        $this->is_active = true;
        $this->save();
    }

    /**
     * Deactivate this overall ranking.
     */
    public function deactivate(): void
    {
        $this->is_active = false;
        $this->save();
    }

    /**
     * Calculate overall score for a contestant.
     */
    public function calculateScore(Contestant $contestant): float
    {
        $totalScore = 0;

        foreach ($this->components as $component) {
            if ($component->type === 'fixed' && $component->category_id) {
                // Fixed type: single category with direct percentage
                $categoryScore = $this->calculateCategoryScore($contestant, $component->category);
                $componentWeight = $component->percentage / 100;
                $totalScore += $categoryScore * $componentWeight;
            } elseif ($component->type === 'category_percentage') {
                // Category percentage type: multiple categories with individual weights
                $componentScore = 0;
                
                foreach ($component->categoryWeights as $weight) {
                    $categoryScore = $this->calculateCategoryScore($contestant, $weight->category);
                    $weightPercentage = $weight->percentage / 100;
                    $componentScore += $categoryScore * $weightPercentage;
                }
                
                $totalScore += $componentScore;
            }
        }

        return round($totalScore, 2);
    }

    /**
     * Calculate the score for a specific category.
     */
    private function calculateCategoryScore(Contestant $contestant, Category $category): float
    {
        // Get all scores for this contestant in this category
        $scores = Score::where('contestant_id', $contestant->id)
            ->where('category_id', $category->id)
            ->get();

        if ($scores->isEmpty()) {
            return 0;
        }

        // Calculate category score (weighted average based on criteria)
        $categoryScore = 0;
        foreach ($category->criteria as $criterion) {
            $criterionScores = $scores->where('criterion_id', $criterion->id);
            
            if ($criterionScores->isEmpty()) {
                continue;
            }

            $averageScore = $criterionScores->avg('score_value');
            // Sum the average scores directly (no weighting needed as max_score already represents the weight)
            $categoryScore += $averageScore;
        }

        return $categoryScore;
    }

    /**
     * Get all contestants with their calculated overall scores.
     */
    public function getContestantScores(): array
    {
        $contestants = Contestant::active()->orderBy('contestant_number')->get();
        $results = [];

        foreach ($contestants as $contestant) {
            $results[] = [
                'contestant' => $contestant,
                'score' => $this->calculateScore($contestant),
            ];
        }

        // Sort by score descending
        usort($results, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        // Add rank
        foreach ($results as $index => &$result) {
            $result['rank'] = $index + 1;
        }

        return $results;
    }
}
