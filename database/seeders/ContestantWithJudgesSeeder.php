<?php

namespace Database\Seeders;

use App\Enums\CategoryStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Contestant;
use App\Models\Criterion;
use App\Models\Score;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ContestantWithJudgesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 3 judges (or get existing ones)
        $judges = collect([
            User::firstOrCreate(
                ['email' => 'maria.santos@pageant.test'],
                [
                    'name' => 'Maria Santos',
                    'password' => Hash::make('password'),
                    'role' => UserRole::JUDGE,
                ]
            ),
            User::firstOrCreate(
                ['email' => 'john.rivera@pageant.test'],
                [
                    'name' => 'John Rivera',
                    'password' => Hash::make('password'),
                    'role' => UserRole::JUDGE,
                ]
            ),
            User::firstOrCreate(
                ['email' => 'ana.cruz@pageant.test'],
                [
                    'name' => 'Ana Cruz',
                    'password' => Hash::make('password'),
                    'role' => UserRole::JUDGE,
                ]
            ),
        ]);

        // Create contestants
        $contestants = collect([
            Contestant::create([
                'name' => 'Isabella Martinez',
                'gender' => 'Female',
                'contestant_number' => 'C101',
                'profile_details' => 'Age 22, Business Administration student',
                'is_active' => true,
            ]),
            Contestant::create([
                'name' => 'Sophia Garcia',
                'gender' => 'Female',
                'contestant_number' => 'C102',
                'profile_details' => 'Age 21, Psychology major',
                'is_active' => true,
            ]),
            Contestant::create([
                'name' => 'Emma Rodriguez',
                'gender' => 'Female',
                'contestant_number' => 'C103',
                'profile_details' => 'Age 23, Marketing professional',
                'is_active' => true,
            ]),
        ]);

        // Create a category
        $category = Category::create([
            'name' => 'Talent Competition',
            'description' => 'Contestants showcase their unique talents and skills',
            'order' => 1,
            'status' => CategoryStatus::ACTIVE,
        ]);

        // Create criteria for the category
        $criteria = collect([
            Criterion::create([
                'category_id' => $category->id,
                'name' => 'Skill & Technique',
                'description' => 'Technical proficiency and execution',
                'percentage' => 30,
                'max_score' => 100,
            ]),
            Criterion::create([
                'category_id' => $category->id,
                'name' => 'Creativity',
                'description' => 'Originality and artistic expression',
                'percentage' => 25,
                'max_score' => 100,
            ]),
            Criterion::create([
                'category_id' => $category->id,
                'name' => 'Stage Presence',
                'description' => 'Confidence and audience engagement',
                'percentage' => 25,
                'max_score' => 100,
            ]),
            Criterion::create([
                'category_id' => $category->id,
                'name' => 'Overall Impact',
                'description' => 'General impression and entertainment value',
                'percentage' => 20,
                'max_score' => 100,
            ]),
        ]);

        // Assign all judges to the category
        $category->judges()->attach($judges->pluck('id'));

        // Create scores: each judge scores each contestant on each criterion
        foreach ($judges as $judge) {
            foreach ($contestants as $contestant) {
                foreach ($criteria as $criterion) {
                    // Generate realistic scores between 70-95
                    $scoreValue = round(fake()->randomFloat(2, 70, 95), 2);

                    Score::create([
                        'judge_id' => $judge->id,
                        'contestant_id' => $contestant->id,
                        'criterion_id' => $criterion->id,
                        'category_id' => $category->id,
                        'score_value' => $scoreValue,
                        'submitted_at' => now(),
                    ]);
                }
            }
        }

        $this->command->info('✓ Seeder completed successfully!');
        $this->command->newLine();
        $this->command->info('Created:');
        $this->command->info('  • 3 Judges:');
        $this->command->info('    - maria.santos@pageant.test');
        $this->command->info('    - john.rivera@pageant.test');
        $this->command->info('    - ana.cruz@pageant.test');
        $this->command->info('  • 3 Contestants: C101, C102, C103');
        $this->command->info('  • 1 Category: Talent Competition (4 criteria)');
        $this->command->info('  • 36 Scores (3 judges × 3 contestants × 4 criteria)');
        $this->command->newLine();
        $this->command->info('Default password for all users: password');
    }
}
