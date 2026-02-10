<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Contestant;
use App\Models\Criterion;
use App\Models\Score;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;

class SampleScoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder creates sample scores for all contestants, judges, categories, and criteria.
     * Scores are randomly generated within realistic ranges based on criterion percentages.
     */
    public function run(): void
    {
        $this->command->info('Generating sample scores...');
        
        // Get all judges
        $judges = User::where('role', UserRole::JUDGE->value)->get();
        
        // Get all contestants
        $contestants = Contestant::all();
        
        // Get all categories with their criteria
        $categories = Category::with('criteria')->get();
        
        $totalScores = 0;
        
        foreach ($judges as $judge) {
            // Get contestants that match this judge's gender assignment
            $judgeGender = $judge->judging_gender;
            $assignedContestants = $contestants->where('gender', $judgeGender);
            
            foreach ($assignedContestants as $contestant) {
                foreach ($categories as $category) {
                    foreach ($category->criteria as $criterion) {
                        // Generate a realistic score based on criterion percentage
                        // Use 70-100% of the maximum score for variety
                        $maxScore = $criterion->percentage;
                        $minScore = $maxScore * 0.70; // 70% of max
                        $score = round(mt_rand($minScore * 100, $maxScore * 100) / 100, 2);
                        
                        Score::create([
                            'judge_id' => $judge->id,
                            'contestant_id' => $contestant->id,
                            'criterion_id' => $criterion->id,
                            'category_id' => $category->id,
                            'score_value' => $score,
                            'submitted_at' => now(),
                        ]);
                        
                        $totalScores++;
                    }
                }
            }
        }
        
        $this->command->info("✓ Sample scores generated successfully ({$totalScores} scores created)");
        $this->command->info("  - {$judges->count()} judges");
        $this->command->info("  - {$contestants->count()} contestants");
        $this->command->info("  - {$categories->count()} categories");
    }
}
