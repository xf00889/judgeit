<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Score;
use App\Models\User;
use Illuminate\View\View;

class ScoresheetController extends Controller
{
    /**
     * Display scoresheet submissions overview - list of judges.
     */
    public function index(): View
    {
        // Get all judges with their assigned categories
        $judges = User::where('role', 'judge')
            ->with('assignedCategories')
            ->orderBy('name')
            ->get();

        // Get all categories for reference
        $categories = Category::orderBy('order')->get();

        // Calculate submission stats for each judge
        $judges->each(function ($judge) {
            $assignedCount = $judge->assignedCategories->count();
            $submittedCount = 0;
            
            foreach ($judge->assignedCategories as $category) {
                $hasSubmitted = Score::where('judge_id', $judge->id)
                    ->where('category_id', $category->id)
                    ->exists();
                if ($hasSubmitted) {
                    $submittedCount++;
                }
            }
            
            $judge->assigned_count = $assignedCount;
            $judge->submitted_count = $submittedCount;
            $judge->pending_count = $assignedCount - $submittedCount;
        });

        return view('admin.scoresheets.index', compact('judges', 'categories'));
    }

    /**
     * Display detailed scores for a specific judge across all categories.
     */
    public function show(User $judge): View
    {
        // Get all categories assigned to this judge
        $categories = $judge->assignedCategories()
            ->with('criteria')
            ->orderBy('order')
            ->get();

        // Get scores for each category
        $categoriesWithScores = $categories->map(function ($category) use ($judge) {
            $scores = Score::with(['contestant', 'criterion'])
                ->where('judge_id', $judge->id)
                ->where('category_id', $category->id)
                ->get();

            $scoresByContestant = $scores->groupBy('contestant_id');
            
            return [
                'category' => $category,
                'scores' => $scoresByContestant,
                'has_submitted' => $scores->isNotEmpty(),
            ];
        });

        return view('admin.scoresheets.show', compact('judge', 'categoriesWithScores'));
    }

    /**
     * Display print view for a specific judge's scoresheets.
     */
    public function print(User $judge): View
    {
        // Get all categories assigned to this judge
        $categories = $judge->assignedCategories()
            ->with('criteria')
            ->orderBy('order')
            ->get();

        // Get scores for each category
        $categoriesWithScores = $categories->map(function ($category) use ($judge) {
            $scores = Score::with(['contestant', 'criterion'])
                ->where('judge_id', $judge->id)
                ->where('category_id', $category->id)
                ->get();

            $scoresByContestant = $scores->groupBy('contestant_id');
            
            return [
                'category' => $category,
                'scores' => $scoresByContestant,
                'has_submitted' => $scores->isNotEmpty(),
            ];
        });

        return view('admin.scoresheets.print', compact('judge', 'categoriesWithScores'));
    }
}
