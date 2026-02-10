<?php

namespace Database\Factories;

use App\Enums\CategoryStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'order' => fake()->numberBetween(1, 10),
            'status' => CategoryStatus::DRAFT,
        ];
    }

    /**
     * Indicate that the category is in draft status.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CategoryStatus::DRAFT,
        ]);
    }

    /**
     * Indicate that the category is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CategoryStatus::ACTIVE,
        ]);
    }

    /**
     * Indicate that the category is locked.
     */
    public function locked(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CategoryStatus::LOCKED,
        ]);
    }

    /**
     * Indicate that the category is finalized.
     */
    public function finalized(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CategoryStatus::FINALIZED,
        ]);
    }
}
