<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Criterion;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Picture Analysis',
                'criteria' => [
                    ['name' => 'Content and relevance', 'percentage' => 30],
                    ['name' => 'Clarity of thought', 'percentage' => 25],
                    ['name' => 'Confidence and Delivery', 'percentage' => 20],
                    ['name' => 'Values and sensitivity', 'percentage' => 15],
                    ['name' => 'Depth of Insight', 'percentage' => 10],
                ],
            ],
            [
                'name' => 'Production Number',
                'criteria' => [
                    ['name' => 'Execution and timing', 'percentage' => 30],
                    ['name' => 'Stage Presence and Confidence', 'percentage' => 25],
                    ['name' => 'Projection and Expression', 'percentage' => 20],
                    ['name' => 'Costume and grooming', 'percentage' => 15],
                    ['name' => 'Overall impact', 'percentage' => 10],
                ],
            ],
            [
                'name' => 'Denim and Diamond Attire',
                'criteria' => [
                    ['name' => 'Style and theme interpretation', 'percentage' => 30],
                    ['name' => 'Poise and bearing', 'percentage' => 25],
                    ['name' => 'Grooming and Overall appearance', 'percentage' => 20],
                    ['name' => 'Stage presence', 'percentage' => 15],
                    ['name' => 'Overall impact', 'percentage' => 10],
                ],
            ],
            [
                'name' => 'Advocacy',
                'criteria' => [
                    ['name' => 'Clarity of advocacy', 'percentage' => 30],
                    ['name' => 'Relevance and significance', 'percentage' => 25],
                    ['name' => 'Delivery and communication', 'percentage' => 20],
                    ['name' => 'Passion and sincerity', 'percentage' => 15],
                    ['name' => 'Overall impact', 'percentage' => 10],
                ],
            ],
            [
                'name' => 'Professional Attire',
                'criteria' => [
                    ['name' => 'Appropriateness and professional look', 'percentage' => 30],
                    ['name' => 'Grooming and neatness', 'percentage' => 25],
                    ['name' => 'Poise and bearing', 'percentage' => 20],
                    ['name' => 'Stage Presence', 'percentage' => 15],
                    ['name' => 'Overall Impact', 'percentage' => 10],
                ],
            ],
            [
                'name' => 'Talent',
                'criteria' => [
                    ['name' => 'Talent Skill and Ability', 'percentage' => 30],
                    ['name' => 'Performance and execution', 'percentage' => 25],
                    ['name' => 'Stage Presence', 'percentage' => 20],
                    ['name' => 'Creativity and presentation', 'percentage' => 15],
                    ['name' => 'Overall impact', 'percentage' => 10],
                ],
            ],
            [
                'name' => 'Gown or Formal Wear',
                'criteria' => [
                    ['name' => 'Elegance and style', 'percentage' => 30],
                    ['name' => 'Poise and Bearing', 'percentage' => 25],
                    ['name' => 'Grooming and overall appearance', 'percentage' => 20],
                    ['name' => 'Stage Presence', 'percentage' => 15],
                    ['name' => 'Overall Impact', 'percentage' => 10],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            $category = Category::create([
                'name' => $categoryData['name'],
            ]);

            foreach ($categoryData['criteria'] as $criterionData) {
                Criterion::create([
                    'category_id' => $category->id,
                    'name' => $criterionData['name'],
                    'percentage' => $criterionData['percentage'],
                ]);
            }
        }

        $this->command->info('✓ Categories seeded successfully (7 categories with criteria)');
    }
}
