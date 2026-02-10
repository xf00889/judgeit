<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('Starting database seeding...');
        $this->command->newLine();

        $this->call([
            UserSeeder::class,
            ContestantSeeder::class,
            CategorySeeder::class,
            JudgeAssignmentSeeder::class,
            ActivateCategoriesSeeder::class,
            OverallRankingSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('🎉 All seeders completed successfully!');
    }
}
