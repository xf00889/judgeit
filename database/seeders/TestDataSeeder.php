<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates minimal user data for quick testing:
     * - 1 admin, 2 judges, 1 tabulator
     * 
     * Note: Contestants and categories are excluded to allow manual setup
     */
    public function run(): void
    {
        // Delete existing test data
        User::whereIn('email', [
            'admin@test.local',
            'judge1@test.local',
            'judge2@test.local',
            'tabulator@test.local',
        ])->delete();

        // Create users
        $admin = User::factory()->admin()->create([
            'name' => 'Test Admin',
            'email' => 'admin@test.local',
        ]);

        $judge1 = User::factory()->judge()->create([
            'name' => 'Test Judge 1',
            'email' => 'judge1@test.local',
        ]);

        $judge2 = User::factory()->judge()->create([
            'name' => 'Test Judge 2',
            'email' => 'judge2@test.local',
        ]);

        $tabulator = User::factory()->tabulator()->create([
            'name' => 'Test Tabulator',
            'email' => 'tabulator@test.local',
        ]);

        $this->command->info('Test data seeded successfully!');
        $this->command->info('Users created:');
        $this->command->info('  - Admin: admin@test.local (password: password)');
        $this->command->info('  - Judge 1: judge1@test.local (password: password)');
        $this->command->info('  - Judge 2: judge2@test.local (password: password)');
        $this->command->info('  - Tabulator: tabulator@test.local (password: password)');
        $this->command->info('');
        $this->command->info('Note: Contestants and categories not included.');
        $this->command->info('Use the admin interface to create them manually.');
    }
}
