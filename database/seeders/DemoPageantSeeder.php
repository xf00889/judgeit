<?php

namespace Database\Seeders;

use App\Enums\CategoryStatus;
use App\Models\Contestant;
use App\Models\Criterion;
use App\Models\Score;
use App\Models\Category;
use App\Models\User;
use Database\Factories\ContestantFactory;
use Illuminate\Database\Seeder;

class DemoPageantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset contestant number counter for consistent numbering
        ContestantFactory::resetCounter();

        // Create users
        $admin = User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@pageant.test',
        ]);

        $judges = collect([
            User::factory()->judge()->create(['name' => 'Judge 1', 'email' => 'judge1@pageant.test']),
            User::factory()->judge()->create(['name' => 'Judge 2', 'email' => 'judge2@pageant.test']),
            User::factory()->judge()->create(['name' => 'Judge 3', 'email' => 'judge3@pageant.test']),
            User::factory()->judge()->create(['name' => 'Judge 4', 'email' => 'judge4@pageant.test']),
            User::factory()->judge()->create(['name' => 'Judge 5', 'email' => 'judge5@pageant.test']),
        ]);

        $tabulators = collect([
            User::factory()->tabulator()->create(['name' => 'Tabulator 1', 'email' => 'tabulator1@pageant.test']),
            User::factory()->tabulator()->create(['name' => 'Tabulator 2', 'email' => 'tabulator2@pageant.test']),
        ]);

        // Create 10 contestants
        $contestants = Contestant::factory()->count(10)->create();

        // Create 3 categories with specific percentages
        $swimwear = Category::factory()->finalized()->create([
            'name' => 'Swimwear',
            'description' => 'Contestants showcase their physical fitness and stage presence in swimwear.',
            'order' => 1,
        ]);

        $eveningGown = Category::factory()->finalized()->create([
            'name' => 'Evening Gown',
            'description' => 'Contestants display elegance and poise in formal evening wear.',
            'order' => 2,
        ]);

        $qa = Category::factory()->finalized()->create([
            'name' => 'Question & Answer',
            'description' => 'Contestants demonstrate intelligence, articulation, and personality through interview questions.',
            'order' => 3,
        ]);

        // Create criteria for Swimwear category
        $swimwearCriteria = [
            Criterion::create([
                'category_id' => $swimwear->id,
                'name' => 'Physical Fitness',
                'description' => 'Overall physical conditioning and health',
                'percentage' => 25,
            ]),
            Criterion::create([
                'category_id' => $swimwear->id,
                'name' => 'Stage Presence',
                'description' => 'Confidence and charisma on stage',
                'percentage' => 25,
            ]),
            Criterion::create([
                'category_id' => $swimwear->id,
                'name' => 'Poise',
                'description' => 'Grace and composure',
                'percentage' => 25,
            ]),
            Criterion::create([
                'category_id' => $swimwear->id,
                'name' => 'Overall Impression',
                'description' => 'General impact and presentation',
                'percentage' => 25,
            ]),
        ];

        // Create criteria for Evening Gown category
        $eveningGownCriteria = [
            Criterion::create([
                'category_id' => $eveningGown->id,
                'name' => 'Elegance',
                'description' => 'Refinement and sophistication',
                'percentage' => 30,
            ]),
            Criterion::create([
                'category_id' => $eveningGown->id,
                'name' => 'Poise & Carriage',
                'description' => 'Posture and movement',
                'percentage' => 30,
            ]),
            Criterion::create([
                'category_id' => $eveningGown->id,
                'name' => 'Gown Selection',
                'description' => 'Appropriateness and style of gown',
                'percentage' => 20,
            ]),
            Criterion::create([
                'category_id' => $eveningGown->id,
                'name' => 'Overall Presentation',
                'description' => 'Complete visual impact',
                'percentage' => 20,
            ]),
        ];

        // Create criteria for Q&A category
        $qaCriteria = [
            Criterion::create([
                'category_id' => $qa->id,
                'name' => 'Articulation',
                'description' => 'Clarity and eloquence of speech',
                'percentage' => 25,
            ]),
            Criterion::create([
                'category_id' => $qa->id,
                'name' => 'Intelligence',
                'description' => 'Depth and quality of response',
                'percentage' => 25,
            ]),
            Criterion::create([
                'category_id' => $qa->id,
                'name' => 'Personality',
                'description' => 'Charm and authenticity',
                'percentage' => 25,
            ]),
            Criterion::create([
                'category_id' => $qa->id,
                'name' => 'Composure',
                'description' => 'Confidence under pressure',
                'percentage' => 25,
            ]),
        ];

        // Assign all judges to all categories
        foreach ([$swimwear, $eveningGown, $qa] as $category) {
            $category->judges()->attach($judges->pluck('id'));
        }

        // Create sample scores for demonstration
        // For each category, each judge scores each contestant on each criterion
        foreach ([$swimwear, $eveningGown, $qa] as $category) {
            $criteria = $category->criteria;
            
            foreach ($judges as $judge) {
                foreach ($contestants as $contestant) {
                    foreach ($criteria as $criterion) {
                        // Generate realistic scores (0-100 scale based on percentage weight)
                        $scoreValue = round(fake()->randomFloat(2, 60, 95), 2);

                        Score::create([
                            'judge_id' => $judge->id,
                            'contestant_id' => $contestant->id,
                            'criterion_id' => $criterion->id,
                            'category_id' => $category->id,
                            'score_value' => $scoreValue,
                            'submitted_at' => now()->subDays(rand(1, 7)),
                        ]);
                    }
                }
            }
        }

        $this->command->info('Demo pageant data seeded successfully!');
        $this->command->info('Users created:');
        $this->command->info('  - Admin: admin@pageant.test (password: password)');
        $this->command->info('  - Judges: judge1@pageant.test through judge5@pageant.test (password: password)');
        $this->command->info('  - Tabulators: tabulator1@pageant.test, tabulator2@pageant.test (password: password)');
        $this->command->info('Contestants: 10 contestants (C001 through C010)');
        $this->command->info('Categories: Swimwear, Evening Gown, Q&A');
        $this->command->info('All categories are finalized with complete scores from all judges.');
    }
}
