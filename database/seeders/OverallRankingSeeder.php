<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Contestant;
use App\Models\FixedScore;
use App\Models\OverallRanking;
use App\Models\User;
use Illuminate\Database\Seeder;

class OverallRankingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create "Top 3" overall ranking
        $overallRanking = OverallRanking::create([
            'name' => 'Top 3',
            'description' => 'Top 3 selection',
            'is_active' => true,
        ]);

        // Component 1: Money contest (Fixed type) - 50%
        $moneyComponent = $overallRanking->components()->create([
            'name' => 'Money contest',
            'type' => 'fixed',
            'category_id' => null,
            'percentage' => 50.00,
        ]);

        // Get admin user for entered_by field
        $admin = User::where('role', 'admin')->first();

        // Fixed scores for Money contest
        $fixedScores = [
            // Female contestants
            ['gender' => 'female', 'number' => '1', 'score' => 49.96],
            ['gender' => 'female', 'number' => '2', 'score' => 8.15],
            ['gender' => 'female', 'number' => '3', 'score' => 6.09],
            ['gender' => 'female', 'number' => '4', 'score' => 5.69],
            ['gender' => 'female', 'number' => '5', 'score' => 4.56],
            // Male contestants
            ['gender' => 'male', 'number' => '1', 'score' => 28.16],
            ['gender' => 'male', 'number' => '2', 'score' => 49.86],
            ['gender' => 'male', 'number' => '3', 'score' => 28.42],
            ['gender' => 'male', 'number' => '4', 'score' => 12.39],
            ['gender' => 'male', 'number' => '5', 'score' => 12.97],
        ];

        foreach ($fixedScores as $scoreData) {
            $contestant = Contestant::where('gender', $scoreData['gender'])
                ->where('contestant_number', $scoreData['number'])
                ->first();

            if ($contestant && $admin) {
                FixedScore::create([
                    'overall_ranking_component_id' => $moneyComponent->id,
                    'contestant_id' => $contestant->id,
                    'score' => $scoreData['score'],
                    'notes' => 'Default seeded score',
                    'entered_by' => $admin->id,
                ]);
            }
        }

        // Component 2: Beauty (Category percentage type) - 25%
        $beautyComponent = $overallRanking->components()->create([
            'name' => 'Beauty',
            'type' => 'category_percentage',
            'category_id' => null,
            'percentage' => 25.00,
        ]);

        // Beauty category weights
        $beautyCategories = [
            'Production Number' => 5.00,
            'Denim and Diamond Attire' => 5.00,
            'Professional Attire' => 5.00,
            'Talent' => 5.00,
            'Gown or Formal Wear' => 5.00,
        ];

        foreach ($beautyCategories as $categoryName => $percentage) {
            $category = Category::where('name', $categoryName)->first();
            if ($category) {
                $beautyComponent->categoryWeights()->create([
                    'category_id' => $category->id,
                    'percentage' => $percentage,
                ]);
            }
        }

        // Component 3: Brains (Category percentage type) - 25%
        $brainsComponent = $overallRanking->components()->create([
            'name' => 'Brains',
            'type' => 'category_percentage',
            'category_id' => null,
            'percentage' => 25.00,
        ]);

        // Brains category weights
        $brainsCategories = [
            'Advocacy' => 10.00,
            'Picture Analysis' => 15.00,
        ];

        foreach ($brainsCategories as $categoryName => $percentage) {
            $category = Category::where('name', $categoryName)->first();
            if ($category) {
                $brainsComponent->categoryWeights()->create([
                    'category_id' => $category->id,
                    'percentage' => $percentage,
                ]);
            }
        }

        $this->command->info('✓ Overall ranking "Top 3" seeded successfully');
        $this->command->info('  - Money contest: 50.00% (Fixed) with 10 default scores');
        $this->command->info('  - Beauty: 25.00% (5 categories)');
        $this->command->info('  - Brains: 25.00% (2 categories)');
    }
}
