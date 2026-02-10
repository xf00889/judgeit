<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;

class JudgeAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all judges
        $judges = User::where('role', UserRole::JUDGE->value)->get();
        
        // Get all categories
        $categories = Category::all();
        
        // Assign all judges to all categories (sync will replace existing assignments)
        foreach ($categories as $category) {
            $category->judges()->sync($judges->pluck('id'));
        }

        $this->command->info('✓ Judge assignments completed successfully (all judges assigned to all categories)');
    }
}
