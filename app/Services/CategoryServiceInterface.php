<?php

namespace App\Services;

use App\Models\Category;
use App\Models\User;

interface CategoryServiceInterface
{
    /**
     * Create a new category.
     *
     * @param array $data
     * @return Category
     */
    public function createCategory(array $data): Category;

    /**
     * Update an existing category.
     *
     * @param Category $category
     * @param array $data
     * @return Category
     */
    public function updateCategory(Category $category, array $data): Category;

    /**
     * Activate a category, making it available for judging.
     *
     * @param Category $category
     * @return void
     * @throws \Exception
     */
    public function activateCategory(Category $category): void;

    /**
     * Deactivate a category, making it unavailable for judging.
     *
     * @param Category $category
     * @return void
     */
    public function deactivateCategory(Category $category): void;

    /**
     * Assign a judge to a category.
     *
     * @param Category $category
     * @param User $judge
     * @return void
     * @throws \Exception
     */
    public function assignJudge(Category $category, User $judge): void;

    /**
     * Remove a judge from a category.
     *
     * @param Category $category
     * @param User $judge
     * @return void
     */
    public function removeJudge(Category $category, User $judge): void;
}
