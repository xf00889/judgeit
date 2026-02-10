<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Contestant;
use App\Models\Criterion;
use App\Models\Score;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;

class ProductionNumberScoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder creates scores for 3 male judges scoring male contestants
     * in the Production Number category only.
     */
    public function run(): void
    {
        $this->command->info('Generating Production Number scores for male judges...');
        
        // Get the Production Number category
        $category = Category::where('name', 'Production Number')->first();
        
        if (!$category) {
            $this->command->error('Production Number category not found. Please run CategorySeeder first.');
            return;
        }
        
        // Get 3 male judges (judge1, judge2, judge3)
        $judges = User::where('role', UserRole::JUDGE->value)
            ->where('judging_gender', 'male')
            ->limit(3)
            ->get();
        
        if ($judges->count() < 3) {
            $this->command->error('Not enough male judges found. Please run UserSeeder first.');
            return;
        }
        
        // Get all male contestants
        $contestants = Contestant::where('gender', 'male')->get();
        
        if ($contestants->isEmpty()) {
            $this->command->error('No male contestants found. Please run ContestantSeeder first.');
            return;
        }
        
        // Get all criteria for Production Number category
        $criteria = $category->criteria;
        
        if ($criteria->isEmpty()) {
            $this->command->error('No criteria found for Production Number category.');
            return;
        }
        
        $totalScores = 0;
        
        // Sample realistic scores for each judge-contestant combination
        $scorePatterns = [
            // Judge 1 - Tends to score higher
            1 => [
                'Execution and timing' => [85, 90, 88, 92, 87],
                'Stage Presence and Confidence' => [82, 88, 85, 90, 84],
                'Projection and Expression' => [80, 85, 83, 88, 82],
                'Costume and grooming' => [78, 82, 80, 85, 79],
                'Overall impact' => [8, 9, 8.5, 9.5, 8],
            ],
            // Judge 2 - Moderate scorer
            2 => [
                'Execution and timing' => [80, 85, 83, 88, 82],
                'Stage Presence and Confidence' => [78, 83, 80, 85, 79],
                'Projection and Expression' => [75, 80, 78, 83, 77],
                'Costume and grooming' => [73, 78, 75, 80, 74],
                'Overall impact' => [7.5, 8, 7.8, 8.5, 7.5],
            ],
            // Judge 3 - Stricter scorer
            3 => [
                'Execution and timing' => [75, 80, 78, 83, 77],
                'Stage Presence and Confidence' => [73, 78, 75, 80, 74],
                'Projection and Expression' => [70, 75, 73, 78, 72],
                'Costume and grooming' => [68, 73, 70, 75, 69],
                'Overall impact' => [7, 7.5, 7.2, 8, 7],
            ],
        ];
        
        foreach ($judges as $judgeIndex => $judge) {
            $judgeNumber = $judgeIndex + 1;
            
            foreach ($contestants as $contestantIndex => $contestant) {
                foreach ($criteria as $criterion) {
                    // Get the score pattern for this judge and criterion
                    $criterionName = $criterion->name;
                    $baseScore = $scorePatterns[$judgeNumber][$criterionName][$contestantIndex] ?? 0;
                    
                    // Add slight variation (+/- 2%)
                    $variation = mt_rand(-200, 200) / 100;
                    $score = round($baseScore + $variation, 2);
                    
                    // Ensure score doesn't exceed criterion percentage
                    $score = min($score, $criterion->percentage);
                    $score = max($score, 0);
                    
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
        
        $this->command->info("✓ Production Number scores generated successfully");
        $this->command->info("  - {$judges->count()} male judges");
        $this->command->info("  - {$contestants->count()} male contestants");
        $this->command->info("  - {$criteria->count()} criteria");
        $this->command->info("  - {$totalScores} total scores created");
    }
}
