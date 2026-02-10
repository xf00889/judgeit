<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('123');

        // Admin account
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => $password,
                'role' => UserRole::ADMIN->value,
            ]
        );

        // Tabulator accounts
        for ($i = 1; $i <= 2; $i++) {
            User::updateOrCreate(
                ['email' => "tabulator{$i}@example.com"],
                [
                    'name' => "Tabulator {$i}",
                    'password' => $password,
                    'role' => UserRole::TABULATOR->value,
                ]
            );
        }

        // Judge accounts for male contestants
        for ($i = 1; $i <= 3; $i++) {
            User::updateOrCreate(
                ['email' => "judge{$i}@example.com"],
                [
                    'name' => "Judge {$i} (Male)",
                    'password' => $password,
                    'role' => UserRole::JUDGE->value,
                    'judging_gender' => 'male',
                ]
            );
        }

        // Judge accounts for female contestants
        for ($i = 4; $i <= 6; $i++) {
            User::updateOrCreate(
                ['email' => "judge{$i}@example.com"],
                [
                    'name' => "Judge {$i} (Female)",
                    'password' => $password,
                    'role' => UserRole::JUDGE->value,
                    'judging_gender' => 'female',
                ]
            );
        }

        $this->command->info('✓ Users seeded successfully (1 admin, 2 tabulators, 6 judges)');
    }
}
