<?php

namespace Database\Seeders;

use App\Models\Contestant;
use Illuminate\Database\Seeder;

class ContestantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contestants = [
            // Female contestants
            ['contestant_number' => '1', 'name' => 'Sweet Shineth', 'gender' => 'female'],
            ['contestant_number' => '2', 'name' => 'Krisia', 'gender' => 'female'],
            ['contestant_number' => '3', 'name' => 'Rica Elaine', 'gender' => 'female'],
            ['contestant_number' => '4', 'name' => 'Cristine Joy', 'gender' => 'female'],
            ['contestant_number' => '5', 'name' => 'Daisy Ann', 'gender' => 'female'],
            
            // Male contestants
            ['contestant_number' => '1', 'name' => 'Kirby', 'gender' => 'male'],
            ['contestant_number' => '2', 'name' => 'John Lewis', 'gender' => 'male'],
            ['contestant_number' => '3', 'name' => 'Clark James', 'gender' => 'male'],
            ['contestant_number' => '4', 'name' => 'Hagin Smith', 'gender' => 'male'],
            ['contestant_number' => '5', 'name' => 'Jayrold', 'gender' => 'male'],
        ];

        foreach ($contestants as $contestant) {
            Contestant::create($contestant);
        }

        $this->command->info('✓ Contestants seeded successfully (5 female, 5 male)');
    }
}
