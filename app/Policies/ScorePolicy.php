<?php

namespace App\Policies;

use App\Models\Score;
use App\Models\Category;
use App\Models\User;

class ScorePolicy
{
    /**
     * Determine whether the user can view any scores.
     */
    public function viewAny(User $user): bool
    {
        // Tabulators and admins can view all scores
        return $user->isTabulator() || $user->isAdmin();
    }

    /**
     * Determine whether the user can view the score.
     */
    public function view(User $user, Score $score): bool
    {
        // Tabulators and admins can view scores
        // Judges can view their own scores
        return $user->isTabulator() 
            || $user->isAdmin() 
            || ($user->isJudge() && $score->judge_id === $user->id);
    }

    /**
     * Determine whether the user can create scores.
     * This is used for general score creation permission.
     */
    public function create(User $user): bool
    {
        // Only judges can create scores
        return $user->isJudge();
    }

    /**
     * Determine whether the user can submit scores for a specific category.
     * This is the primary authorization check for score submission.
     */
    public function submit(User $user, Category $category): bool
    {
        \Log::info('ScorePolicy::submit check', [
            'user_id' => $user->id,
            'user_role' => $user->role->value,
            'category_id' => $category->id,
            'category_status' => $category->status->value,
        ]);

        // User must be a judge
        if (!$user->isJudge()) {
            \Log::warning('User is not a judge', ['user_id' => $user->id, 'role' => $user->role->value]);
            return false;
        }

        // Category must be active
        if (!$category->isActive()) {
            \Log::warning('Category is not active', ['category_id' => $category->id, 'status' => $category->status->value]);
            return false;
        }

        // Load judges relationship if not already loaded
        if (!$category->relationLoaded('judges')) {
            $category->load('judges');
        }

        $judgeIds = $category->judges->pluck('id')->toArray();
        \Log::info('Checking judge assignment', [
            'user_id' => $user->id,
            'assigned_judges' => $judgeIds,
            'is_assigned' => in_array($user->id, $judgeIds),
        ]);

        // Judge must be assigned to this category
        if (!in_array($user->id, $judgeIds)) {
            \Log::warning('Judge not assigned to category', ['user_id' => $user->id, 'category_id' => $category->id]);
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can update the score.
     */
    public function update(User $user, Score $score): bool
    {
        // Scores cannot be updated once submitted
        // This enforces immutability of submitted scores
        return false;
    }

    /**
     * Determine whether the user can delete the score.
     */
    public function delete(User $user, Score $score): bool
    {
        // Only admins can delete scores, and only if category is not finalized
        return $user->isAdmin() && !$score->category->isFinalized();
    }

    /**
     * Determine whether the user can view results (aggregated scores).
     */
    public function viewResults(User $user): bool
    {
        // Tabulators and admins can view results
        return $user->isTabulator() || $user->isAdmin();
    }
}
