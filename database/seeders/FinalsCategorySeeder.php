<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Criterion;
use Illuminate\Database\Seeder;

class FinalsCategorySeeder extends Seeder
{
    /**
     * Seed the finals category with Beauty and Brain criteria.
     */
    public function run(): void
    {
        // Check if Finals category already exists
        $existingFinalsCategory = Category::where('is_finals', true)->first();
        
        if ($existingFinalsCategory) {
            $this->command->warn('Finals category already exists. Skipping creation.');
            return;
        }

        // Create Finals category
        $finalsCategory = Category::create([
            'name' => 'Finals',
            'description' => 'Final round for top 3 contestants. Beauty 50% + Brain 50%.',
            'order' => 999,
            'status' => 'draft',
            'is_finals' => true,
        ]);

        // Create Beauty criterion (50%)
        Criterion::create([
            'category_id' => $finalsCategory->id,
            'name' => 'Beauty',
            'description' => 'Overall beauty, poise, and stage presence',
            'max_score' => 100,
            'percentage' => 50.00,
        ]);

        // Create Brain criterion (50%)
        Criterion::create([
            'category_id' => $finalsCategory->id,
            'name' => 'Brain',
            'description' => 'Intelligence, wit, and communication skills',
            'max_score' => 100,
            'percentage' => 50.00,
        ]);

        $this->command->info('Finals category created with Beauty and Brain criteria.');
    }
}
