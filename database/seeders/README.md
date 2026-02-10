# Database Seeders

This directory contains seeders for populating the Beauty Pageant Judging System with test and demonstration data.

## Available Seeders

### ProductionNumberScoreSeeder
Creates scores for 3 male judges scoring male contestants in the Production Number category only.

**Usage:**
```bash
php artisan db:seed --class=ProductionNumberScoreSeeder
```

**Prerequisites:**
- UserSeeder must be run first (creates judges)
- ContestantSeeder must be run first (creates contestants)
- CategorySeeder must be run first (creates Production Number category)

**Creates:**
- 75 scores (3 judges × 5 male contestants × 5 criteria)
- Realistic score variations per judge (strict, moderate, lenient)
- Scores range from 70-92% of maximum criterion value

**Note:** This seeder demonstrates the minimum 3-judge requirement for category winners to appear on the awards page.

### TestDataSeeder
Creates minimal data for quick testing and development.

**Usage:**
```bash
php artisan migrate:fresh --seed --seeder=TestDataSeeder
```

**Creates:**
- 1 Admin user
- 2 Judge users
- 1 Tabulator user
- 3 Contestants (C001, C002, C003)
- 1 Active category with 2 criteria (50 points each)
- Both judges assigned to the category

**Login Credentials:**
- Admin: `admin@test.local` / `password`
- Judge 1: `judge1@test.local` / `password`
- Judge 2: `judge2@test.local` / `password`
- Tabulator: `tabulator@test.local` / `password`

### DemoPageantSeeder
Creates a complete pageant setup with realistic data for demonstration purposes.

**Usage:**
```bash
php artisan migrate:fresh --seed --seeder=DemoPageantSeeder
```

**Creates:**
- 1 Admin user
- 5 Judge users
- 2 Tabulator users
- 10 Contestants (C001 through C010)
- 3 Finalized categories:
  - Swimwear (30%) - 4 criteria
  - Evening Gown (40%) - 4 criteria
  - Question & Answer (30%) - 4 criteria
- All judges assigned to all categories
- Complete scores from all judges for all contestants (600 total scores)

**Login Credentials:**
- Admin: `admin@pageant.test` / `password`
- Judges: `judge1@pageant.test` through `judge5@pageant.test` / `password`
- Tabulators: `tabulator1@pageant.test`, `tabulator2@pageant.test` / `password`

**Note:** All categories are finalized with complete scoring, so you can immediately view rankings and results.

## Factories

The following factories are available for creating test data:

### UserFactory
```php
User::factory()->admin()->create();
User::factory()->judge()->create();
User::factory()->tabulator()->create();
```

### ContestantFactory
```php
Contestant::factory()->create();
Contestant::factory()->inactive()->create();

// Reset counter for consistent numbering
ContestantFactory::resetCounter();
```

### CategoryFactory
```php
Category::factory()->draft()->create();
Category::factory()->active()->create();
Category::factory()->locked()->create();
Category::factory()->finalized()->create();
```

### CriterionFactory
```php
Criterion::factory()->create();
Criterion::factory()->withMaxScore(100)->create();
```

### ScoreFactory
```php
// Note: Score factory requires explicit relationships
Score::create([
    'judge_id' => $judge->id,
    'contestant_id' => $contestant->id,
    'criterion_id' => $criterion->id,
    'category_id' => $category->id,
    'score_value' => 85.5,
    'submitted_at' => now(),
]);
```

## Tips

1. **Quick Testing**: Use `TestDataSeeder` for rapid development and testing of individual features.

2. **Demo/Presentation**: Use `DemoPageantSeeder` to showcase the complete system with realistic data.

3. **Custom Seeding**: Create your own seeder by extending `Illuminate\Database\Seeder` and using the factories.

4. **Resetting Data**: Always use `migrate:fresh` before seeding to ensure a clean database state.

5. **Production**: Never run seeders in production environments. They are for development and testing only.
