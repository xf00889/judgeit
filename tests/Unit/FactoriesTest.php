<?php

namespace Tests\Unit;

use App\Enums\CategoryStatus;
use App\Enums\UserRole;
use App\Models\Contestant;
use App\Models\Criterion;
use App\Models\Score;
use App\Models\Category;
use App\Models\User;
use Database\Factories\ContestantFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FactoriesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_factory_creates_users_with_different_roles()
    {
        $admin = User::factory()->admin()->create();
        $judge = User::factory()->judge()->create();
        $tabulator = User::factory()->tabulator()->create();

        $this->assertEquals(UserRole::ADMIN, $admin->role);
        $this->assertEquals(UserRole::JUDGE, $judge->role);
        $this->assertEquals(UserRole::TABULATOR, $tabulator->role);
    }

    /** @test */
    public function contestant_factory_creates_contestants_with_unique_numbers()
    {
        ContestantFactory::resetCounter();
        
        $contestant1 = Contestant::factory()->create();
        $contestant2 = Contestant::factory()->create();
        $contestant3 = Contestant::factory()->create();

        $this->assertEquals('C001', $contestant1->contestant_number);
        $this->assertEquals('C002', $contestant2->contestant_number);
        $this->assertEquals('C003', $contestant3->contestant_number);
        $this->assertTrue($contestant1->is_active);
    }

    /** @test */
    public function category_factory_creates_categories_with_different_statuses()
    {
        $draft = Category::factory()->draft()->create();
        $active = Category::factory()->active()->create();
        $locked = Category::factory()->locked()->create();
        $finalized = Category::factory()->finalized()->create();

        $this->assertEquals(CategoryStatus::DRAFT, $draft->status);
        $this->assertEquals(CategoryStatus::ACTIVE, $active->status);
        $this->assertEquals(CategoryStatus::LOCKED, $locked->status);
        $this->assertEquals(CategoryStatus::FINALIZED, $finalized->status);
    }

    /** @test */
    public function criterion_factory_creates_criteria_with_category()
    {
        $category = Category::factory()->create();
        $criterion = Criterion::create([
            'category_id' => $category->id,
            'name' => 'Test Criterion',
            'description' => 'Test description',
            'percentage' => 100,
        ]);

        $this->assertEquals($category->id, $criterion->category_id);
        $this->assertEquals(100, $criterion->percentage);
    }

    /** @test */
    public function score_can_be_created_with_valid_relationships()
    {
        $judge = User::factory()->judge()->create();
        $contestant = Contestant::factory()->create();
        $category = Category::factory()->create();
        $criterion = Criterion::create([
            'category_id' => $category->id,
            'name' => 'Test',
            'description' => 'Test',
            'percentage' => 100,
        ]);

        $score = Score::create([
            'judge_id' => $judge->id,
            'contestant_id' => $contestant->id,
            'criterion_id' => $criterion->id,
            'category_id' => $category->id,
            'score_value' => 85.5,
            'submitted_at' => now(),
        ]);

        $this->assertEquals($judge->id, $score->judge_id);
        $this->assertEquals($contestant->id, $score->contestant_id);
        $this->assertEquals($criterion->id, $score->criterion_id);
        $this->assertEquals(85.5, $score->score_value);
    }
}
