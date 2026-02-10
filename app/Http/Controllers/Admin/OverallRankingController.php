<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OverallRankingRequest;
use App\Models\Category;
use App\Models\Contestant;
use App\Models\OverallRanking;
use App\Services\OverallRankingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OverallRankingController extends Controller
{
    public function __construct(
        private OverallRankingService $overallRankingService
    ) {}

    /**
     * Display a listing of overall rankings.
     */
    public function index(): View
    {
        $rankings = OverallRanking::with('components.category', 'components.categoryWeights.category')->get();
        
        return view('admin.overall-rankings.index', compact('rankings'));
    }

    /**
     * Show the form for creating a new overall ranking.
     */
    public function create(): View
    {
        $categories = Category::orderBy('order')->get();
        
        return view('admin.overall-rankings.create', compact('categories'));
    }

    /**
     * Store a newly created overall ranking.
     */
    public function store(OverallRankingRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();
            
            $overallRanking = OverallRanking::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_active' => false,
            ]);

            // Create components
            foreach ($validated['components'] as $componentData) {
                $component = $overallRanking->components()->create([
                    'name' => $componentData['name'],
                    'type' => $componentData['type'],
                    'category_id' => $componentData['category_id'] ?? null,
                    'percentage' => $componentData['percentage'],
                ]);

                // Create category weights if type is category_percentage
                if ($componentData['type'] === 'category_percentage' && isset($componentData['category_weights'])) {
                    foreach ($componentData['category_weights'] as $weightData) {
                        $component->categoryWeights()->create([
                            'category_id' => $weightData['category_id'],
                            'percentage' => $weightData['percentage'],
                        ]);
                    }
                }
            }

            return redirect()
                ->route('admin.overall-rankings.index')
                ->with('success', 'Overall ranking created successfully.');
        } catch (\Exception $e) {
            \Log::error('Overall ranking store error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error creating overall ranking: ' . $e->getMessage());
        }
    }

    /**
     * Display the results for a specific overall ranking.
     */
    public function show(OverallRanking $overallRanking): View
    {
        $overallRanking->load('components.category.criteria', 'components.categoryWeights.category.criteria');
        $results = $this->overallRankingService->getContestantsWithScores($overallRanking);
        
        return view('admin.overall-rankings.show', compact('overallRanking', 'results'));
    }

    /**
     * Show the form for editing an overall ranking.
     */
    public function edit(OverallRanking $overallRanking): View
    {
        $overallRanking->load('components.category', 'components.categoryWeights.category');
        $categories = Category::orderBy('order')->get();
        
        return view('admin.overall-rankings.edit', compact('overallRanking', 'categories'));
    }

    /**
     * Update the specified overall ranking.
     */
    public function update(OverallRankingRequest $request, OverallRanking $overallRanking): RedirectResponse
    {
        try {
            $validated = $request->validated();
            
            $overallRanking->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            // Delete existing components and create new ones
            $overallRanking->components()->delete();
            
            foreach ($validated['components'] as $componentData) {
                $component = $overallRanking->components()->create([
                    'name' => $componentData['name'],
                    'type' => $componentData['type'],
                    'category_id' => $componentData['category_id'] ?? null,
                    'percentage' => $componentData['percentage'],
                ]);

                // Create category weights if type is category_percentage
                if ($componentData['type'] === 'category_percentage' && isset($componentData['category_weights'])) {
                    foreach ($componentData['category_weights'] as $weightData) {
                        $component->categoryWeights()->create([
                            'category_id' => $weightData['category_id'],
                            'percentage' => $weightData['percentage'],
                        ]);
                    }
                }
            }

            return redirect()
                ->route('admin.overall-rankings.index')
                ->with('success', 'Overall ranking updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error updating overall ranking: ' . $e->getMessage());
        }
    }

    /**
     * Activate the specified overall ranking.
     */
    public function activate(OverallRanking $overallRanking): RedirectResponse
    {
        try {
            $overallRanking->activate();
            
            return redirect()
                ->route('admin.overall-rankings.index')
                ->with('success', 'Overall ranking activated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.overall-rankings.index')
                ->with('error', 'Error activating overall ranking: ' . $e->getMessage());
        }
    }

    /**
     * Deactivate the specified overall ranking.
     */
    public function deactivate(OverallRanking $overallRanking): RedirectResponse
    {
        try {
            $overallRanking->deactivate();
            
            return redirect()
                ->route('admin.overall-rankings.index')
                ->with('success', 'Overall ranking deactivated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.overall-rankings.index')
                ->with('error', 'Error deactivating overall ranking: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified overall ranking.
     */
    public function destroy(OverallRanking $overallRanking): RedirectResponse
    {
        try {
            if ($overallRanking->is_active) {
                return redirect()
                    ->route('admin.overall-rankings.index')
                    ->with('error', 'Cannot delete an active overall ranking. Deactivate it first.');
            }
            
            $overallRanking->delete();
            
            return redirect()
                ->route('admin.overall-rankings.index')
                ->with('success', 'Overall ranking deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.overall-rankings.index')
                ->with('error', 'Error deleting overall ranking: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for managing fixed scores.
     */
    public function manageFixedScores(OverallRanking $overallRanking): View
    {
        $overallRanking->load('components.fixedScores');
        
        // Get all fixed type components
        $fixedComponents = $overallRanking->components->where('type', 'fixed');
        
        // Get all contestants
        $contestants = Contestant::orderBy('contestant_number')->get();
        
        // Organize existing scores by contestant and component
        $existingScores = [];
        foreach ($fixedComponents as $component) {
            foreach ($component->fixedScores as $score) {
                $existingScores[$score->contestant_id][$component->id] = $score;
            }
        }
        
        return view('admin.overall-rankings.fixed-scores', compact('overallRanking', 'fixedComponents', 'contestants', 'existingScores'));
    }

    /**
     * Save fixed scores for an overall ranking.
     */
    public function saveFixedScores(\Illuminate\Http\Request $request, OverallRanking $overallRanking): RedirectResponse
    {
        try {
            // Get all fixed components to validate against their percentages
            $fixedComponents = $overallRanking->components()
                ->where('type', 'fixed')
                ->get()
                ->keyBy('id');

            $validated = $request->validate([
                'scores' => 'required|array',
                'scores.*' => 'array',
                'scores.*.*' => 'nullable|numeric|min:0',
                'notes' => 'nullable|array',
                'notes.*' => 'array',
                'notes.*.*' => 'nullable|string|max:500',
            ]);

            $scores = $validated['scores'] ?? [];
            $notes = $validated['notes'] ?? [];

            // Validate each score against its component's percentage
            foreach ($scores as $contestantId => $componentScores) {
                foreach ($componentScores as $componentId => $score) {
                    if ($score !== null && $score !== '') {
                        $component = $fixedComponents->get($componentId);
                        if (!$component) {
                            throw new \Exception("Invalid component ID: {$componentId}");
                        }
                        
                        $maxScore = (float) $component->percentage;
                        if ($score > $maxScore) {
                            throw new \Illuminate\Validation\ValidationException(
                                \Illuminate\Support\Facades\Validator::make([], [])
                                    ->errors()
                                    ->add("scores.{$contestantId}.{$componentId}", "The score must not exceed {$maxScore} (component percentage).")
                            );
                        }

                        \App\Models\FixedScore::updateOrCreate(
                            [
                                'overall_ranking_component_id' => $componentId,
                                'contestant_id' => $contestantId,
                            ],
                            [
                                'score' => $score,
                                'notes' => $notes[$contestantId][$componentId] ?? null,
                                'entered_by' => auth()->id(),
                            ]
                        );
                    }
                }
            }

            return redirect()
                ->route('admin.overall-rankings.index')
                ->with('success', 'Fixed scores saved successfully.');
        } catch (\Exception $e) {
            \Log::error('Fixed scores save error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error saving fixed scores: ' . $e->getMessage());
        }
    }
}
