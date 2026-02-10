<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Collection;

interface CategoryRepositoryInterface
{
    /**
     * Get the currently active category.
     *
     * @return Category|null
     */
    public function getActiveCategory(): ?Category;

    /**
     * Get all finalized categories.
     *
     * @return Collection
     */
    public function getFinalizedCategories(): Collection;

    /**
     * Get all categories assigned to a specific judge.
     *
     * @param User $judge
     * @return Collection
     */
    public function getCategoriesForJudge(User $judge): Collection;
}
