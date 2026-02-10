<?php

namespace App\Http\Controllers\Judge;

use App\Http\Controllers\Controller;
use App\Http\Requests\ScoreSubmissionRequest;
use App\Models\Category;
use App\Services\ScoringServiceInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JudgeController extends Controller
{
    use AuthorizesRequests;
    public function __construct(
        private ScoringServiceInterface $scoringService
    ) {}

    /**
     * Display a listing of assigned active categories for the judge.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $judge = Auth::user();
        
        // Get categories assigned to this judge
        $categories = $judge->assignedCategories()
            ->with('criteria')
            ->orderBy('order')
            ->get();
        
        // Add submission status to each category
        $categories->each(function ($category) use ($judge) {
            $category->has_submitted = $this->scoringService->hasJudgeSubmitted($judge, $category);
        });
        
        return view('judge.index', compact('categories'));
    }

    /**
     * Display the judging form for a specific category.
     *
     * @param Category $category
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function show(Category $category, Request $request)
    {
        $judge = Auth::user();
        
        // Check if user is authenticated and is a judge
        if (!$judge || !$judge->isJudge()) {
            abort(403, 'Access denied. Judge role required.');
        }
        
        // Handle score caching - save current scores to session if provided
        $scores = $request->input('scores', []);
        if (!empty($scores)) {
            $cacheKey = "judge_scores_{$judge->id}_{$category->id}";
            session()->put($cacheKey, $scores);
        }
        
        // Get judging form data from service
        try {
            $formData = $this->scoringService->getJudgingForm($judge, $category);
            
            // Get contestant index from request, default to 0 (first contestant)
            $contestantIndex = (int) $request->get('contestant', 0);
            $contestants = $formData['contestants'];
            
            // Check if there are any contestants
            if ($contestants->isEmpty()) {
                return redirect()
                    ->route('judge.index')
                    ->with('error', 'No active contestants found for this category.');
            }
            
            // Validate contestant index
            if ($contestantIndex < 0 || $contestantIndex >= $contestants->count()) {
                $contestantIndex = 0;
            }
            
            // Get current contestant
            $currentContestant = $contestants->get($contestantIndex);
            
            // Get cached scores from session
            $cacheKey = "judge_scores_{$judge->id}_{$category->id}";
            $cachedScores = session()->get($cacheKey, []);
            
            // Add pagination data
            $formData['currentContestant'] = $currentContestant;
            $formData['contestantIndex'] = $contestantIndex;
            $formData['totalContestants'] = $contestants->count();
            $formData['hasNext'] = $contestantIndex < ($contestants->count() - 1);
            $formData['hasPrevious'] = $contestantIndex > 0;
            $formData['nextIndex'] = $contestantIndex + 1;
            $formData['previousIndex'] = $contestantIndex - 1;
            $formData['cachedScores'] = $cachedScores;
            
            return view('judge.show', $formData);
        } catch (\Exception $e) {
            return redirect()
                ->route('judge.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Process score submission for a category.
     *
     * @param ScoreSubmissionRequest $request
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitScores(ScoreSubmissionRequest $request, Category $category)
    {
        $judge = Auth::user();
        
        // Authorize using ScorePolicy - check the policy directly
        $policy = new \App\Policies\ScorePolicy();
        if (!$policy->submit($judge, $category)) {
            throw new \Illuminate\Auth\Access\AuthorizationException('Not authorized to submit scores for this category');
        }
        
        // Get validated data
        $validated = $request->validated();
        
        try {
            // Submit scores through service
            $this->scoringService->submitScores($judge, $category, $validated['scores']);
            
            // Clear cached scores after successful submission
            $cacheKey = "judge_scores_{$judge->id}_{$category->id}";
            session()->forget($cacheKey);
            
            return redirect()
                ->route('judge.index')
                ->with('success', 'Scores submitted successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
}
