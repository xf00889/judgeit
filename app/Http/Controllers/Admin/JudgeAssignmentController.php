<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\User;
use App\Services\CategoryServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JudgeAssignmentController extends Controller
{
    /**
     * Create a new controller instance.
     * 
     * Note: Authorization is handled by route middleware (role:admin)
     */
    public function __construct(
        private CategoryServiceInterface $categoryService
    ) {
    }

    /**
     * Display judge assignments for a category.
     */
    public function index(Category $category): View
    {
        $assignedJudges = $category->judges()->orderBy('name')->get();
        $availableJudges = User::where('role', UserRole::JUDGE)
            ->whereNotIn('id', $assignedJudges->pluck('id'))
            ->orderBy('name')
            ->get();

        return view('admin.judges.index', compact('category', 'assignedJudges', 'availableJudges'));
    }

    /**
     * Assign a judge to a category.
     */
    public function store(Request $request, Category $category): RedirectResponse
    {
        $request->validate([
            'judge_ids' => ['required', 'array', 'min:1'],
            'judge_ids.*' => ['required', 'exists:users,id'],
        ]);

        try {
            $assignedCount = 0;
            $errors = [];

            foreach ($request->judge_ids as $judgeId) {
                try {
                    $judge = User::findOrFail($judgeId);
                    $this->categoryService->assignJudge($category, $judge);
                    $assignedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Failed to assign {$judge->name}: {$e->getMessage()}";
                }
            }

            if ($assignedCount > 0) {
                $message = $assignedCount === 1 
                    ? 'Judge assigned successfully.' 
                    : "{$assignedCount} judges assigned successfully.";
                
                if (!empty($errors)) {
                    $message .= ' However, some assignments failed.';
                }

                return redirect()
                    ->route('admin.judges.index', $category)
                    ->with('success', $message)
                    ->with('warnings', $errors);
            }

            return redirect()
                ->route('admin.judges.index', $category)
                ->with('error', 'Failed to assign judges: ' . implode(', ', $errors));
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.judges.index', $category)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove a judge from a category.
     */
    public function destroy(Category $category, User $judge): RedirectResponse
    {
        try {
            $this->categoryService->removeJudge($category, $judge);

            return redirect()
                ->route('admin.judges.index', $category)
                ->with('success', 'Judge removed successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.judges.index', $category)
                ->with('error', $e->getMessage());
        }
    }
}
