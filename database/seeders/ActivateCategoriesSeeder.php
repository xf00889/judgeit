<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Enums\CategoryStatus;
use Illuminate\Database\Seeder;

class ActivateCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $count = Category::query()->update([
            'status' => CategoryStatus::ACTIVE->value,
        ]);

        $this->command->info("✓ Categories activated successfully ({$count} categories)");
    }
}
