<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    /**
     * Determine whether the user can view any categories.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view categories
        return $user->isAdmin() || $user->isJudge() || $user->isTabulator();
    }

    /**
     * Determine whether the user can view the category.
     */
    public function view(User $user, Category $category): bool
    {
        // All authenticated users can view individual categories
        return $user->isAdmin() || $user->isJudge() || $user->isTabulator();
    }

    /**
     * Determine whether the user can create categories.
     */
    public function create(User $user): bool
    {
        // Only admins can create categories
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the category.
     */
    public function update(User $user, Category $category): bool
    {
        // Only admins can update categories, and only if not finalized
        return $user->isAdmin() && !$category->isFinalized();
    }

    /**
     * Determine whether the user can delete the category.
     */
    public function delete(User $user, Category $category): bool
    {
        // Only admins can delete categories, and only if not finalized
        return $user->isAdmin() && !$category->isFinalized();
    }

    /**
     * Determine whether the user can activate the category.
     */
    public function activate(User $user, Category $category): bool
    {
        // Only admins can activate categories
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can lock the category.
     */
    public function lock(User $user, Category $category): bool
    {
        // Only admins can lock categories
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can finalize the category.
     */
    public function finalize(User $user, Category $category): bool
    {
        // Only admins can finalize categories
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can judge (submit scores for) the category.
     */
    public function judge(User $user, Category $category): bool
    {
        // Only judges assigned to this category can judge it
        return $user->isJudge() && $category->judges->contains($user);
    }

    /**
     * Determine whether the user can assign judges to the category.
     */
    public function assignJudge(User $user, Category $category): bool
    {
        // Only admins can assign judges
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can remove judges from the category.
     */
    public function removeJudge(User $user, Category $category): bool
    {
        // Only admins can remove judges
        return $user->isAdmin();
    }
}
