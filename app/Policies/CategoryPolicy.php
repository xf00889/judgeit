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
        return $user->isAdmin() || $user->isJudge() || $user->isTabulator();
    }

    /**
     * Determine whether the user can view the category.
     */
    public function view(User $user, Category $category): bool
    {
        return $user->isAdmin() || $user->isJudge() || $user->isTabulator();
    }

    /**
     * Determine whether the user can create categories.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the category.
     */
    public function update(User $user, Category $category): bool
    {
        return $user->isAdmin() && !$category->isFinalized();
    }

    /**
     * Determine whether the user can delete the category.
     */
    public function delete(User $user, Category $category): bool
    {
        return $user->isAdmin() && !$category->isFinalized();
    }

    /**
     * Determine whether the user can activate the category.
     */
    public function activate(User $user, Category $category): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can lock the category.
     */
    public function lock(User $user, Category $category): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can finalize the category.
     */
    public function finalize(User $user, Category $category): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can judge (submit scores for) the category.
     */
    public function judge(User $user, Category $category): bool
    {
        return $user->isJudge() && $category->judges->contains($user);
    }

    /**
     * Determine whether the user can assign judges to the category.
     */
    public function assignJudge(User $user, Category $category): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can remove judges from the category.
     */
    public function removeJudge(User $user, Category $category): bool
    {
        return $user->isAdmin();
    }
}
