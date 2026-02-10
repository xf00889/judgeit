<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Criterion>
 */
class CriterionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'percentage' => fake()->randomElement([10, 20, 25, 50]),
        ];
    }

    /**
     * Set a specific category for this criterion.
     */
    public function forCategory(Category $category): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => $category->id,
        ]);
    }

    /**
     * Set a specific percentage for this criterion.
     */
    public function withPercentage(float $percentage): static
    {
        return $this->state(fn (array $attributes) => [
            'percentage' => $percentage,
        ]);
    }
}
