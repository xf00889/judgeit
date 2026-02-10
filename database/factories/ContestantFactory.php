<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contestant>
 */
class ContestantFactory extends Factory
{
    /**
     * Counter for generating unique contestant numbers.
     */
    private static int $contestantNumberCounter = 1;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'gender' => fake()->randomElement(['Male', 'Female', 'Other']),
            'contestant_number' => 'C' . str_pad((string) self::$contestantNumberCounter++, 3, '0', STR_PAD_LEFT),
            'profile_details' => fake()->paragraph(),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the contestant is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Reset the contestant number counter (useful for testing).
     */
    public static function resetCounter(): void
    {
        self::$contestantNumberCounter = 1;
    }
}
