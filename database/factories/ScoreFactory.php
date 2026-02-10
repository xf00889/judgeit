<?php

namespace Database\Factories;

use App\Models\Contestant;
use App\Models\Criterion;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Score>
 */
class ScoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Note: This factory requires explicit relationships to be set
        // Use forJudgeContestantCriterion() method to create valid scores
        return [
            'judge_id' => null,
            'contestant_id' => null,
            'criterion_id' => null,
            'category_id' => null,
            'score_value' => 0,
            'submitted_at' => now(),
        ];
    }

    /**
     * Set specific relationships for this score.
     */
    public function forJudgeContestantCriterion(User $judge, Contestant $contestant, Criterion $criterion): static
    {
        return $this->state(fn (array $attributes) => [
            'judge_id' => $judge->id,
            'contestant_id' => $contestant->id,
            'criterion_id' => $criterion->id,
            'category_id' => $criterion->category_id,
            'score_value' => fake()->randomFloat(2, 0, $criterion->max_score),
        ]);
    }

    /**
     * Set a specific score value.
     */
    public function withValue(float $value): static
    {
        return $this->state(fn (array $attributes) => [
            'score_value' => $value,
        ]);
    }

    /**
     * Set a specific submission timestamp.
     */
    public function submittedAt(\DateTimeInterface $timestamp): static
    {
        return $this->state(fn (array $attributes) => [
            'submitted_at' => $timestamp,
        ]);
    }
}
