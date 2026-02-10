<?php

namespace App\Repositories;

use App\Enums\CategoryStatus;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    /**
     * Get the currently active category.
     *
     * @return Category|null
     */
    public function getActiveCategory(): ?Category
    {
        return Category::where('status', CategoryStatus::ACTIVE)
            ->with(['criteria', 'judges'])
            ->first();
    }

    /**
     * Get all finalized categories.
     *
     * @return Collection
     */
    public function getFinalizedCategories(): Collection
    {
        return Category::where('status', CategoryStatus::FINALIZED)
            ->with(['criteria'])
            ->orderBy('order')
            ->get();
    }

    /**
     * Get all categories assigned to a specific judge.
     *
     * @param User $judge
     * @return Collection
     */
    public function getCategoriesForJudge(User $judge): Collection
    {
        return $judge->assignedCategories()
            ->with(['criteria'])
            ->orderBy('order')
            ->get();
    }


}
