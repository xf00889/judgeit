<?php

namespace App\Services;

use App\Enums\CategoryStatus;
use App\Models\Category;
use App\Models\User;
use App\Repositories\CategoryRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CategoryService implements CategoryServiceInterface
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
        private \App\Services\AuditLogService $auditLogService
    ) {}

    /**
     * Create a new category.
     *
     * @param array $data
     * @return Category
     */
    public function createCategory(array $data): Category
    {
        return Category::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'order' => $data['order'] ?? 0,
            'status' => CategoryStatus::DRAFT,
        ]);
    }

    /**
     * Update an existing category.
     *
     * @param Category $category
     * @param array $data
     * @return Category
     */
    public function updateCategory(Category $category, array $data): Category
    {
        $category->update($data);
        $category->refresh();

        return $category;
    }

    /**
     * Activate a category, making it available for judging.
     *
     * @param Category $category
     * @return void
     * @throws \Exception
     */
    public function activateCategory(Category $category): void
    {
        DB::transaction(function () use ($category) {
            // Validate category has criteria
            if ($category->criteria()->count() === 0) {
                throw new \Exception("Cannot activate category without criteria.");
            }

            $oldStatus = $category->status->value;
            
            // Activate the category (allow multiple active categories)
            $category->activate();
            
            // Log the state change
            $this->auditLogService->logCategoryStateChange(
                $category->id,
                $category->name,
                $oldStatus,
                $category->status->value,
                auth()->id()
            );
        });
    }

    /**
     * Deactivate a category, making it unavailable for judging.
     *
     * @param Category $category
     * @return void
     */
    public function deactivateCategory(Category $category): void
    {
        if (!$category->isActive()) {
            throw new \Exception("Category is not active.");
        }

        $oldStatus = $category->status->value;
        $category->update(['status' => CategoryStatus::DRAFT]);
        
        // Log the state change
        $this->auditLogService->logCategoryStateChange(
            $category->id,
            $category->name,
            $oldStatus,
            CategoryStatus::DRAFT->value,
            auth()->id()
        );
    }

    /**
     * Assign a judge to a category.
     *
     * @param Category $category
     * @param User $judge
     * @return void
     * @throws \Exception
     */
    public function assignJudge(Category $category, User $judge): void
    {
        // Validate user is a judge
        if (!$judge->isJudge()) {
            throw new \Exception("User must have judge role to be assigned to a category.");
        }

        // Attach judge if not already assigned
        if (!$category->judges->contains($judge)) {
            $category->judges()->attach($judge->id);
            
            // Log the assignment
            $this->auditLogService->logJudgeAssignment(
                $judge->id,
                $judge->name,
                $category->id,
                $category->name,
                auth()->id()
            );
        }
    }

    /**
     * Remove a judge from a category.
     *
     * @param Category $category
     * @param User $judge
     * @return void
     */
    public function removeJudge(Category $category, User $judge): void
    {
        $category->judges()->detach($judge->id);
        
        // Log the removal
        $this->auditLogService->logJudgeRemoval(
            $judge->id,
            $judge->name,
            $category->id,
            $category->name,
            auth()->id()
        );
    }
}
