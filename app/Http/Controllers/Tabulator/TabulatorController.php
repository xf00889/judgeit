<?php

namespace App\Http\Controllers\Tabulator;

use App\Http\Controllers\Controller;
use App\Models\Contestant;
use App\Models\Category;
use App\Services\ResultServiceInterface;
use App\Services\OverallRankingService;
use Illuminate\Http\Request;

class TabulatorController extends Controller
{
    public function __construct(
        private ResultServiceInterface $resultService,
        private OverallRankingService $overallRankingService
    ) {}

    /**
     * Display real-time results for all contestants.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Get the active overall ranking
        $activeRanking = \App\Models\OverallRanking::where('is_active', true)
            ->with('components.category.criteria', 'components.categoryWeights.category.criteria')
            ->first();

        // Get all active contestants
        $contestants = Contestant::where('is_active', true)
            ->orderBy('contestant_number')
            ->get();

        // Get all categories (for progress display)
        $allCategories = Category::orderBy('order')->get();

        // Calculate scores for each contestant
        if ($activeRanking) {
            $allResults = $this->overallRankingService->getContestantsWithScores($activeRanking);
            
            // Group by gender and calculate separate rankings
            $resultsByGender = $allResults->groupBy(function($result) {
                return $result['contestant']->gender ?? 'Not Specified';
            });
            
            // Recalculate ranks within each gender group
            $results = collect();
            foreach ($resultsByGender as $gender => $genderResults) {
                // Sort by total score descending
                $sorted = $genderResults->sortByDesc('total_score')->values();
                
                // Add rank with tie handling
                $currentRank = 1;
                $previousScore = null;
                $sameRankCount = 0;
                
                $rankedResults = $sorted->map(function ($result, $index) use (&$currentRank, &$previousScore, &$sameRankCount) {
                    if ($previousScore !== null && abs($result['total_score'] - $previousScore) < 0.01) {
                        // Same score as previous - it's a tie
                        $result['rank'] = $currentRank;
                        $result['is_tie'] = true;
                        $sameRankCount++;
                    } else {
                        // Different score - update rank
                        if ($sameRankCount > 0) {
                            $currentRank += $sameRankCount + 1;
                            $sameRankCount = 0;
                        } else if ($index > 0) {
                            $currentRank++;
                        }
                        $result['rank'] = $currentRank;
                        $result['is_tie'] = false;
                    }
                    
                    $previousScore = $result['total_score'];
                    return $result;
                });
                
                $results = $results->merge($rankedResults);
            }
            
            // Map total_score to overall_score for view compatibility
            $results = $results->map(function ($result) {
                $result['overall_score'] = $result['total_score'];
                return $result;
            });
        } else {
            // Fallback: show basic contestant list without scores
            $results = $contestants->map(function ($contestant) {
                return [
                    'contestant' => $contestant,
                    'overall_score' => 0,
                    'total_score' => 0,
                    'rank' => 0,
                    'breakdown' => [],
                ];
            });
        }

        // Get judging progress for all categories
        $progress = $allCategories->map(function ($category) {
            return $this->resultService->getJudgingProgress($category);
        });

        $viewData = [
            'results' => $results,
            'activeRanking' => $activeRanking,
            'progress' => $progress,
        ];

        // If AJAX request, return JSON with partial HTML
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'html' => view('tabulator.results', $viewData)->render(),
                'results' => $results,
                'progress' => $progress,
            ]);
        }

        return view('tabulator.results', $viewData);
    }

    /**
     * Display print-friendly view for real-time results.
     *
     * @return \Illuminate\View\View
     */
    public function printResults(): \Illuminate\View\View
    {
        // Get the active overall ranking
        $activeRanking = \App\Models\OverallRanking::where('is_active', true)
            ->with('components.category.criteria', 'components.categoryWeights.category.criteria')
            ->first();

        if ($activeRanking) {
            $allResults = $this->overallRankingService->getContestantsWithScores($activeRanking);
            
            // Group by gender and calculate separate rankings
            $resultsByGender = $allResults->groupBy(function($result) {
                return $result['contestant']->gender ?? 'Not Specified';
            });
            
            // Recalculate ranks within each gender group
            $results = collect();
            foreach ($resultsByGender as $gender => $genderResults) {
                // Sort by total score descending
                $sorted = $genderResults->sortByDesc('total_score')->values();
                
                // Add rank with tie handling
                $currentRank = 1;
                $previousScore = null;
                $sameRankCount = 0;
                
                $rankedResults = $sorted->map(function ($result, $index) use (&$currentRank, &$previousScore, &$sameRankCount) {
                    if ($previousScore !== null && abs($result['total_score'] - $previousScore) < 0.01) {
                        $result['rank'] = $currentRank;
                        $result['is_tie'] = true;
                        $sameRankCount++;
                    } else {
                        if ($sameRankCount > 0) {
                            $currentRank += $sameRankCount + 1;
                            $sameRankCount = 0;
                        } else if ($index > 0) {
                            $currentRank++;
                        }
                        $result['rank'] = $currentRank;
                        $result['is_tie'] = false;
                    }
                    
                    $previousScore = $result['total_score'];
                    return $result;
                });
                
                $results = $results->merge($rankedResults);
            }
        } else {
            $results = collect();
        }

        return view('tabulator.results-print', compact('activeRanking', 'results'));
    }

    /**
     * Display final rankings for all contestants.
     *
     * @return \Illuminate\View\View
     */
    public function rankings()
    {
        // Get the active overall ranking
        $activeRanking = \App\Models\OverallRanking::where('is_active', true)
            ->with('components.category.criteria', 'components.categoryWeights.category.criteria')
            ->first();

        if ($activeRanking) {
            // Use modern overall ranking system
            $allResults = $this->overallRankingService->getContestantsWithScores($activeRanking);
            
            // Group by gender and calculate separate rankings
            $resultsByGender = $allResults->groupBy(function($result) {
                return $result['contestant']->gender ?? 'Not Specified';
            });
            
            // Recalculate ranks within each gender group
            $rankedResults = collect();
            foreach ($resultsByGender as $gender => $genderResults) {
                // Sort by total score descending
                $sorted = $genderResults->sortByDesc('total_score')->values();
                
                // Add rank with tie handling
                $currentRank = 1;
                $previousScore = null;
                $sameRankCount = 0;
                
                $genderRanked = $sorted->map(function ($result, $index) use (&$currentRank, &$previousScore, &$sameRankCount) {
                    if ($previousScore !== null && abs($result['total_score'] - $previousScore) < 0.01) {
                        // Same score as previous - it's a tie
                        $result['rank'] = $currentRank;
                        $result['is_tie'] = true;
                        $sameRankCount++;
                    } else {
                        // Different score - update rank
                        if ($sameRankCount > 0) {
                            $currentRank += $sameRankCount + 1;
                            $sameRankCount = 0;
                        } else if ($index > 0) {
                            $currentRank++;
                        }
                        $result['rank'] = $currentRank;
                        $result['is_tie'] = false;
                    }
                    
                    $previousScore = $result['total_score'];
                    return $result;
                });
                
                $rankedResults = $rankedResults->merge($genderRanked);
            }
            
            // Convert to RankingResultDTO format for view compatibility
            $rankings = $rankedResults->map(function ($result) {
                // Extract category scores from breakdown
                $categoryScores = [];
                foreach ($result['breakdown'] as $component) {
                    foreach ($component['categories'] as $category) {
                        $categoryScores[$category['name']] = $category['score'];
                    }
                }
                
                return new \App\DTOs\RankingResultDTO(
                    rank: $result['rank'],
                    contestant: $result['contestant'],
                    overallScore: $result['total_score'],
                    categoryScores: $categoryScores
                );
            });
        } else {
            // Fallback to legacy system
            $rankings = $this->resultService->generateRankings();
        }

        // Group rankings by gender
        $rankingsByGender = $rankings->groupBy(function ($ranking) {
            return $ranking->contestant->gender ?? 'Not Specified';
        });

        // Identify top 3 for each gender
        $topThreeByGender = [];
        foreach ($rankingsByGender as $gender => $genderRankings) {
            $topThreeByGender[$gender] = [
                'winner' => $genderRankings->where('rank', 1)->first(),
                'firstRunnerUp' => $genderRankings->where('rank', 2)->first(),
                'secondRunnerUp' => $genderRankings->where('rank', 3)->first(),
            ];
        }

        return view('tabulator.rankings', [
            'rankingsByGender' => $rankingsByGender,
            'topThreeByGender' => $topThreeByGender,
        ]);
    }

    /**
     * Display awards and category winners.
     *
     * @return \Illuminate\View\View
     */
    public function awards()
    {
        // Get the active overall ranking
        $activeRanking = \App\Models\OverallRanking::where('is_active', true)
            ->with('components.category.criteria', 'components.categoryWeights.category.criteria')
            ->first();

        if ($activeRanking) {
            // Use modern overall ranking system
            $allResults = $this->overallRankingService->getContestantsWithScores($activeRanking);
            
            // Group by gender and calculate separate rankings
            $resultsByGender = $allResults->groupBy(function($result) {
                return $result['contestant']->gender ?? 'Not Specified';
            });
            
            // Recalculate ranks within each gender group
            $rankedResults = collect();
            foreach ($resultsByGender as $gender => $genderResults) {
                // Sort by total score descending
                $sorted = $genderResults->sortByDesc('total_score')->values();
                
                // Add rank with tie handling
                $currentRank = 1;
                $previousScore = null;
                $sameRankCount = 0;
                
                $genderRanked = $sorted->map(function ($result, $index) use (&$currentRank, &$previousScore, &$sameRankCount) {
                    if ($previousScore !== null && abs($result['total_score'] - $previousScore) < 0.01) {
                        // Same score as previous - it's a tie
                        $result['rank'] = $currentRank;
                        $result['is_tie'] = true;
                        $sameRankCount++;
                    } else {
                        // Different score - update rank
                        if ($sameRankCount > 0) {
                            $currentRank += $sameRankCount + 1;
                            $sameRankCount = 0;
                        } else if ($index > 0) {
                            $currentRank++;
                        }
                        $result['rank'] = $currentRank;
                        $result['is_tie'] = false;
                    }
                    
                    $previousScore = $result['total_score'];
                    return $result;
                });
                
                $rankedResults = $rankedResults->merge($genderRanked);
            }
            
            // Convert to RankingResultDTO format for view compatibility
            $rankings = $rankedResults->map(function ($result) {
                // Extract category scores from breakdown
                $categoryScores = [];
                foreach ($result['breakdown'] as $component) {
                    foreach ($component['categories'] as $category) {
                        $categoryScores[$category['name']] = $category['score'];
                    }
                }
                
                return new \App\DTOs\RankingResultDTO(
                    rank: $result['rank'],
                    contestant: $result['contestant'],
                    overallScore: $result['total_score'],
                    categoryScores: $categoryScores
                );
            });
        } else {
            // Fallback to legacy system
            $rankings = $this->resultService->generateRankings();
        }

        // Group rankings by gender
        $rankingsByGender = $rankings->groupBy(function ($ranking) {
            return $ranking->contestant->gender ?? 'Not Specified';
        });

        // Identify top 3 for each gender
        $topThreeByGender = [];
        foreach ($rankingsByGender as $gender => $genderRankings) {
            $topThreeByGender[$gender] = [
                'winner' => $genderRankings->where('rank', 1)->first(),
                'firstRunnerUp' => $genderRankings->where('rank', 2)->first(),
                'secondRunnerUp' => $genderRankings->where('rank', 3)->first(),
            ];
        }

        // Get all categories where at least 3 judges have submitted (regardless of status)
        $allCategories = Category::with('judges')
            ->orderBy('order')
            ->get();

        // Filter categories where at least 3 judges have completed submissions
        $completedCategories = $allCategories->filter(function ($category) {
            return $category->hasMinimumJudgesSubmitted();
        });

        // Calculate category winners by gender (best in each completed category per gender)
        $categoryWinnersByGender = [];
        foreach ($completedCategories as $category) {
            // Get all active contestants with their scores for this category
            $contestants = Contestant::where('is_active', true)
                ->orderBy('contestant_number')
                ->get();

            $categoryScores = $contestants->map(function ($contestant) use ($category) {
                $score = $this->resultService->calculateCategoryScore($contestant, $category);
                return [
                    'contestant' => $contestant,
                    'score' => $score,
                    'category_status' => $category->status->value,
                    'category_id' => $category->id,
                    'gender' => $contestant->gender ?? 'Not Specified',
                ];
            })->filter(function ($item) {
                // Only include contestants with scores > 0
                return $item['score'] > 0;
            });

            // Group by gender and find winner for each
            $scoresByGender = $categoryScores->groupBy('gender');
            
            foreach ($scoresByGender as $gender => $genderScores) {
                $categoryWinner = $genderScores->sortByDesc('score')->first();
                
                if ($categoryWinner && $categoryWinner['score'] > 0) {
                    if (!isset($categoryWinnersByGender[$gender])) {
                        $categoryWinnersByGender[$gender] = [];
                    }
                    $categoryWinnersByGender[$gender][$category->name] = $categoryWinner;
                }
            }
        }

        return view('tabulator.awards', [
            'rankingsByGender' => $rankingsByGender,
            'topThreeByGender' => $topThreeByGender,
            'categoryWinnersByGender' => $categoryWinnersByGender,
        ]);
    }

    /**
     * Display print-friendly view for category winners.
     *
     * @return \Illuminate\View\View
     */
    public function printAwards(): \Illuminate\View\View
    {
        try {
            // Get the active overall ranking
            $activeRanking = \App\Models\OverallRanking::where('is_active', true)
                ->with('components.category.criteria', 'components.categoryWeights.category.criteria')
                ->first();

            if ($activeRanking) {
                // Use modern overall ranking system
                $allResults = $this->overallRankingService->getContestantsWithScores($activeRanking);
                
                // Group by gender and calculate separate rankings
                $resultsByGender = $allResults->groupBy(function($result) {
                    return $result['contestant']->gender ?? 'Not Specified';
                });
                
                // Recalculate ranks within each gender group
                $rankedResults = collect();
                foreach ($resultsByGender as $gender => $genderResults) {
                    // Sort by total score descending
                    $sorted = $genderResults->sortByDesc('total_score')->values();
                    
                    // Add rank with tie handling
                    $currentRank = 1;
                    $previousScore = null;
                    $sameRankCount = 0;
                    
                    $genderRanked = $sorted->map(function ($result, $index) use (&$currentRank, &$previousScore, &$sameRankCount) {
                        if ($previousScore !== null && abs($result['total_score'] - $previousScore) < 0.01) {
                            // Same score as previous - it's a tie
                            $result['rank'] = $currentRank;
                            $result['is_tie'] = true;
                            $sameRankCount++;
                        } else {
                            // Different score - update rank
                            if ($sameRankCount > 0) {
                                $currentRank += $sameRankCount + 1;
                                $sameRankCount = 0;
                            } else if ($index > 0) {
                                $currentRank++;
                            }
                            $result['rank'] = $currentRank;
                            $result['is_tie'] = false;
                        }
                        
                        $previousScore = $result['total_score'];
                        return $result;
                    });
                    
                    $rankedResults = $rankedResults->merge($genderRanked);
                }
                
                // Convert to RankingResultDTO format for view compatibility
                $rankings = $rankedResults->map(function ($result) {
                    // Extract category scores from breakdown
                    $categoryScores = [];
                    foreach ($result['breakdown'] as $component) {
                        foreach ($component['categories'] as $category) {
                            $categoryScores[$category['name']] = $category['score'];
                        }
                    }
                    
                    return new \App\DTOs\RankingResultDTO(
                        rank: $result['rank'],
                        contestant: $result['contestant'],
                        overallScore: $result['total_score'],
                        categoryScores: $categoryScores
                    );
                });
            } else {
                // Fallback to legacy system
                $rankings = $this->resultService->generateRankings();
            }

            // Group rankings by gender
            $rankingsByGender = $rankings->groupBy(function ($ranking) {
                return $ranking->contestant->gender ?? 'Not Specified';
            });

            // Identify top 3 for each gender
            $topThreeByGender = [];
            foreach ($rankingsByGender as $gender => $genderRankings) {
                $topThreeByGender[$gender] = [
                    'winner' => $genderRankings->where('rank', 1)->first(),
                    'firstRunnerUp' => $genderRankings->where('rank', 2)->first(),
                    'secondRunnerUp' => $genderRankings->where('rank', 3)->first(),
                ];
            }

            // Get all categories where at least 3 judges have submitted (regardless of status)
            $allCategories = Category::with('judges')
                ->orderBy('order')
                ->get();

            // Filter categories where at least 3 judges have completed submissions
            $completedCategories = $allCategories->filter(function ($category) {
                return $category->hasMinimumJudgesSubmitted();
            });

            // Calculate category winners by gender (best in each completed category per gender)
            $categoryWinnersByGender = [];
            foreach ($completedCategories as $category) {
                // Get all active contestants with their scores for this category
                $contestants = Contestant::where('is_active', true)
                    ->orderBy('contestant_number')
                    ->get();

                $categoryScores = $contestants->map(function ($contestant) use ($category) {
                    $score = $this->resultService->calculateCategoryScore($contestant, $category);
                    return [
                        'contestant' => $contestant,
                        'score' => $score,
                        'category_status' => $category->status->value,
                        'category_id' => $category->id,
                        'gender' => $contestant->gender ?? 'Not Specified',
                    ];
                })->filter(function ($item) {
                    // Only include contestants with scores > 0
                    return $item['score'] > 0;
                });

                // Group by gender and find winner for each
                $scoresByGender = $categoryScores->groupBy('gender');
                
                foreach ($scoresByGender as $gender => $genderScores) {
                    $categoryWinner = $genderScores->sortByDesc('score')->first();
                    
                    if ($categoryWinner && $categoryWinner['score'] > 0) {
                        if (!isset($categoryWinnersByGender[$gender])) {
                            $categoryWinnersByGender[$gender] = [];
                        }
                        $categoryWinnersByGender[$gender][$category->name] = $categoryWinner;
                    }
                }
            }

            return view('tabulator.awards-print', [
                'rankingsByGender' => $rankingsByGender,
                'topThreeByGender' => $topThreeByGender,
                'categoryWinnersByGender' => $categoryWinnersByGender,
            ]);
        } catch (\Exception $e) {
            \Log::error('Print awards error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return view('errors.500', [
                'message' => 'Unable to generate print view. Please try again later.'
            ]);
        }
    }

    /**
     * Display print-friendly view for overall rankings.
     *
     * @return \Illuminate\View\View
     */
    public function printRankings(): \Illuminate\View\View
    {
        try {
            // Get the active overall ranking
            $activeRanking = \App\Models\OverallRanking::where('is_active', true)
                ->with('components.category.criteria', 'components.categoryWeights.category.criteria')
                ->first();

            if ($activeRanking) {
                // Use modern overall ranking system
                $allResults = $this->overallRankingService->getContestantsWithScores($activeRanking);
                
                // Group by gender and calculate separate rankings
                $resultsByGender = $allResults->groupBy(function($result) {
                    return $result['contestant']->gender ?? 'Not Specified';
                });
                
                // Recalculate ranks within each gender group
                $rankedResults = collect();
                foreach ($resultsByGender as $gender => $genderResults) {
                    // Sort by total score descending
                    $sorted = $genderResults->sortByDesc('total_score')->values();
                    
                    // Add rank with tie handling
                    $currentRank = 1;
                    $previousScore = null;
                    $sameRankCount = 0;
                    
                    $genderRanked = $sorted->map(function ($result, $index) use (&$currentRank, &$previousScore, &$sameRankCount) {
                        if ($previousScore !== null && abs($result['total_score'] - $previousScore) < 0.01) {
                            // Same score as previous - it's a tie
                            $result['rank'] = $currentRank;
                            $result['is_tie'] = true;
                            $sameRankCount++;
                        } else {
                            // Different score - update rank
                            if ($sameRankCount > 0) {
                                $currentRank += $sameRankCount + 1;
                                $sameRankCount = 0;
                            } else if ($index > 0) {
                                $currentRank++;
                            }
                            $result['rank'] = $currentRank;
                            $result['is_tie'] = false;
                        }
                        
                        $previousScore = $result['total_score'];
                        return $result;
                    });
                    
                    $rankedResults = $rankedResults->merge($genderRanked);
                }
                
                // Convert to RankingResultDTO format for view compatibility
                $rankings = $rankedResults->map(function ($result) {
                    // Extract category scores from breakdown
                    $categoryScores = [];
                    foreach ($result['breakdown'] as $component) {
                        foreach ($component['categories'] as $category) {
                            $categoryScores[$category['name']] = $category['score'];
                        }
                    }
                    
                    return new \App\DTOs\RankingResultDTO(
                        rank: $result['rank'],
                        contestant: $result['contestant'],
                        overallScore: $result['total_score'],
                        categoryScores: $categoryScores
                    );
                });
            } else {
                // Fallback to legacy system
                $rankings = $this->resultService->generateRankings();
            }

            // Group rankings by gender
            $rankingsByGender = $rankings->groupBy(function ($ranking) {
                return $ranking->contestant->gender ?? 'Not Specified';
            });

            // Identify top 3 for each gender
            $topThreeByGender = [];
            foreach ($rankingsByGender as $gender => $genderRankings) {
                $topThreeByGender[$gender] = [
                    'winner' => $genderRankings->where('rank', 1)->first(),
                    'firstRunnerUp' => $genderRankings->where('rank', 2)->first(),
                    'secondRunnerUp' => $genderRankings->where('rank', 3)->first(),
                ];
            }

            return view('tabulator.rankings-print', [
                'rankingsByGender' => $rankingsByGender,
                'topThreeByGender' => $topThreeByGender,
            ]);
        } catch (\Exception $e) {
            \Log::error('Print rankings error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return view('errors.500', [
                'message' => 'Unable to generate print view. Please try again later.'
            ]);
        }
    }

    /**
     * Display detailed results for a specific category.
     *
     * @param Category $category
     * @return \Illuminate\View\View
     */
    public function categoryResults(Category $category, \Illuminate\Http\Request $request): \Illuminate\View\View
    {
        $category->load('criteria', 'judges');
        
        // Get gender filter from request
        $genderFilter = $request->query('gender');
        
        // Get all active contestants, optionally filtered by gender
        $contestants = Contestant::where('is_active', true)
            ->when($genderFilter, function ($query) use ($genderFilter) {
                return $query->where('gender', $genderFilter);
            })
            ->orderBy('contestant_number')
            ->get();

        // Calculate scores for each contestant
        $results = $contestants->map(function ($contestant) use ($category) {
            $score = $this->resultService->calculateCategoryScore($contestant, $category);
            
            // Get detailed breakdown by criterion
            $criterionBreakdown = [];
            foreach ($category->criteria as $criterion) {
                $scores = \App\Models\Score::where('contestant_id', $contestant->id)
                    ->where('criterion_id', $criterion->id)
                    ->with('judge')
                    ->get();
                
                $criterionBreakdown[$criterion->id] = [
                    'criterion' => $criterion,
                    'scores' => $scores,
                    'average' => $scores->avg('score_value') ?? 0,
                ];
            }
            
            return [
                'contestant' => $contestant,
                'total_score' => $score,
                'criterion_breakdown' => $criterionBreakdown,
            ];
        })->sortByDesc('total_score')->values();

        // Add rank with tie handling
        $results = $this->assignRanksWithTies($results, 'total_score');

        return view('tabulator.category-results', [
            'category' => $category,
            'results' => $results,
            'genderFilter' => $genderFilter,
        ]);
    }

    /**
     * Display print-friendly view for category results.
     *
     * @param Category $category
     * @return \Illuminate\View\View
     */
    public function printCategoryResults(Category $category, \Illuminate\Http\Request $request): \Illuminate\View\View
    {
        $category->load('criteria', 'judges');
        
        // Get gender filter from request
        $genderFilter = $request->query('gender');
        
        // Get all active contestants, optionally filtered by gender
        $contestants = Contestant::where('is_active', true)
            ->when($genderFilter, function ($query) use ($genderFilter) {
                return $query->where('gender', $genderFilter);
            })
            ->orderBy('contestant_number')
            ->get();

        // Calculate scores for each contestant
        $results = $contestants->map(function ($contestant) use ($category) {
            $score = $this->resultService->calculateCategoryScore($contestant, $category);
            
            // Get detailed breakdown by criterion
            $criterionBreakdown = [];
            foreach ($category->criteria as $criterion) {
                $scores = \App\Models\Score::where('contestant_id', $contestant->id)
                    ->where('criterion_id', $criterion->id)
                    ->with('judge')
                    ->get();
                
                $criterionBreakdown[$criterion->id] = [
                    'criterion' => $criterion,
                    'scores' => $scores,
                    'average' => $scores->avg('score_value') ?? 0,
                ];
            }
            
            return [
                'contestant' => $contestant,
                'total_score' => $score,
                'criterion_breakdown' => $criterionBreakdown,
            ];
        })->sortByDesc('total_score')->values();

        // Add rank with tie handling
        $results = $this->assignRanksWithTies($results, 'total_score');

        return view('tabulator.category-results-print', [
            'category' => $category,
            'results' => $results,
            'genderFilter' => $genderFilter,
        ]);
    }

    /**
     * Assign ranks to results, handling ties.
     *
     * @param \Illuminate\Support\Collection $results
     * @param string $scoreKey
     * @return \Illuminate\Support\Collection
     */
    private function assignRanksWithTies($results, $scoreKey = 'total_score')
    {
        $currentRank = 1;
        $previousScore = null;
        $sameRankCount = 0;
        
        return $results->map(function ($result, $index) use (&$currentRank, &$previousScore, &$sameRankCount, $scoreKey) {
            $score = $result[$scoreKey];
            
            // Use a much smaller tolerance for tie detection (0.001 instead of 0.01)
            // This means scores must be within 0.001 to be considered tied
            if ($previousScore !== null && abs($score - $previousScore) < 0.001) {
                // Same score as previous - it's a tie
                $result['rank'] = $currentRank;
                $result['is_tie'] = true;
                $sameRankCount++;
            } else {
                // Different score - update rank
                if ($sameRankCount > 0) {
                    $currentRank += $sameRankCount + 1;
                    $sameRankCount = 0;
                } else if ($index > 0) {
                    $currentRank++;
                }
                $result['rank'] = $currentRank;
                $result['is_tie'] = false;
            }
            
            $previousScore = $score;
            return $result;
        });
    }

    /**
     * Assign ranks to a collection of ranking DTOs.
     *
     * @param \Illuminate\Support\Collection $rankings
     * @return \Illuminate\Support\Collection
     */
    private function assignRanksToCollection($rankings)
    {
        // Sort by overall score descending
        $sorted = $rankings->sortByDesc('overallScore')->values();
        
        $currentRank = 1;
        $previousScore = null;
        $sameRankCount = 0;
        
        return $sorted->map(function ($ranking, $index) use (&$currentRank, &$previousScore, &$sameRankCount) {
            $score = $ranking->overallScore;
            
            // Use a much smaller tolerance for tie detection (0.001 instead of 0.01)
            // This means scores must be within 0.001 to be considered tied
            if ($previousScore !== null && abs($score - $previousScore) < 0.001) {
                // Same score as previous - it's a tie
                $newRank = $currentRank;
                $sameRankCount++;
            } else {
                // Different score - update rank
                if ($sameRankCount > 0) {
                    $currentRank += $sameRankCount + 1;
                    $sameRankCount = 0;
                } else if ($index > 0) {
                    $currentRank++;
                }
                $newRank = $currentRank;
            }
            
            $previousScore = $score;
            
            // Create new DTO with updated rank
            return new \App\DTOs\RankingResultDTO(
                rank: $newRank,
                contestant: $ranking->contestant,
                overallScore: $ranking->overallScore,
                categoryScores: $ranking->categoryScores
            );
        });
    }

    /**
     * Display a listing of overall rankings.
     */
    public function overallRankingsIndex(): \Illuminate\View\View
    {
        $rankings = \App\Models\OverallRanking::with('components.category', 'components.categoryWeights.category')->get();
        
        return view('tabulator.overall-rankings.index', compact('rankings'));
    }

    /**
     * Show the form for creating a new overall ranking.
     */
    public function createOverallRanking(): \Illuminate\View\View
    {
        $categories = Category::orderBy('order')->get();
        
        return view('tabulator.overall-rankings.create', compact('categories'));
    }

    /**
     * Store a newly created overall ranking.
     */
    public function storeOverallRanking(\App\Http\Requests\OverallRankingRequest $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $validated = $request->validated();
            
            $overallRanking = \App\Models\OverallRanking::create([
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
                ->route('tabulator.overall-rankings.index')
                ->with('success', 'Overall ranking created successfully.');
        } catch (\Exception $e) {
            \Log::error('Tabulator overall ranking store error', [
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
     * Show the form for editing an overall ranking.
     */
    public function editOverallRanking(\App\Models\OverallRanking $overallRanking): \Illuminate\View\View
    {
        $overallRanking->load('components.category', 'components.categoryWeights.category');
        $categories = Category::orderBy('order')->get();
        
        return view('tabulator.overall-rankings.edit', compact('overallRanking', 'categories'));
    }

    /**
     * Update the specified overall ranking.
     */
    public function updateOverallRanking(
        \App\Http\Requests\OverallRankingRequest $request,
        \App\Models\OverallRanking $overallRanking
    ): \Illuminate\Http\RedirectResponse
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
                ->route('tabulator.overall-rankings.index')
                ->with('success', 'Overall ranking updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error updating overall ranking: ' . $e->getMessage());
        }
    }

    /**
     * Show the results for a specific overall ranking.
     */
    public function showOverallRanking(\App\Models\OverallRanking $overallRanking): \Illuminate\View\View
    {
        $overallRanking->load('components.category', 'components.categoryWeights.category');
        $allResults = $this->overallRankingService->getContestantsWithScores($overallRanking);
        
        // Group by gender and calculate separate rankings
        $resultsByGender = $allResults->groupBy(function($result) {
            return $result['contestant']->gender ?? 'Not Specified';
        });
        
        // Recalculate ranks within each gender group
        $rankedResultsByGender = collect();
        foreach ($resultsByGender as $gender => $genderResults) {
            // Sort by total score descending
            $sorted = $genderResults->sortByDesc('total_score')->values();
            
            // Add rank with tie handling
            $currentRank = 1;
            $previousScore = null;
            $sameRankCount = 0;
            
            $rankedResults = $sorted->map(function ($result, $index) use (&$currentRank, &$previousScore, &$sameRankCount) {
                if ($previousScore !== null && abs($result['total_score'] - $previousScore) < 0.01) {
                    // Same score as previous - it's a tie
                    $result['rank'] = $currentRank;
                    $result['is_tie'] = true;
                    $sameRankCount++;
                } else {
                    // Different score - update rank
                    if ($sameRankCount > 0) {
                        $currentRank += $sameRankCount + 1;
                        $sameRankCount = 0;
                    } else if ($index > 0) {
                        $currentRank++;
                    }
                    $result['rank'] = $currentRank;
                    $result['is_tie'] = false;
                }
                
                $previousScore = $result['total_score'];
                return $result;
            });
            
            $rankedResultsByGender[$gender] = $rankedResults;
        }
        
        // Flatten for backward compatibility with $results variable
        $results = $rankedResultsByGender->flatten(1);
        
        return view('tabulator.overall-rankings.show', compact('overallRanking', 'results', 'rankedResultsByGender'));
    }

    /**
     * Activate the specified overall ranking.
     */
    public function activateOverallRanking(\App\Models\OverallRanking $overallRanking): \Illuminate\Http\RedirectResponse
    {
        $overallRanking->activate();
        
        return redirect()
            ->route('tabulator.overall-rankings.index')
            ->with('success', 'Overall ranking activated successfully.');
    }

    /**
     * Deactivate the specified overall ranking.
     */
    public function deactivateOverallRanking(\App\Models\OverallRanking $overallRanking): \Illuminate\Http\RedirectResponse
    {
        $overallRanking->deactivate();
        
        return redirect()
            ->route('tabulator.overall-rankings.index')
            ->with('success', 'Overall ranking deactivated successfully.');
    }

    /**
     * Show the form for managing fixed scores.
     */
    public function manageFixedScores(\App\Models\OverallRanking $overallRanking): \Illuminate\View\View
    {
        $overallRanking->load('components.fixedScores');
        
        // Get all fixed type components
        $fixedComponents = $overallRanking->components->where('type', 'fixed');
        
        // Get all contestants
        $contestants = \App\Models\Contestant::orderBy('contestant_number')->get();
        
        // Organize existing scores by contestant and component
        $existingScores = [];
        foreach ($fixedComponents as $component) {
            foreach ($component->fixedScores as $score) {
                $existingScores[$score->contestant_id][$component->id] = $score;
            }
        }
        
        return view('tabulator.overall-rankings.fixed-scores', compact('overallRanking', 'fixedComponents', 'contestants', 'existingScores'));
    }

    /**
     * Save fixed scores for an overall ranking.
     */
    public function saveFixedScores(Request $request, \App\Models\OverallRanking $overallRanking): \Illuminate\Http\RedirectResponse
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
                ->route('tabulator.overall-rankings.index')
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

    /**
     * Display scoresheet submissions overview - list of judges.
     */
    public function scoresheets()
    {
        // Get all judges with their assigned categories
        $judges = \App\Models\User::where('role', 'judge')
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
                $hasSubmitted = \App\Models\Score::where('judge_id', $judge->id)
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

        return view('tabulator.scoresheets', compact('judges', 'categories'));
    }

    /**
     * Display detailed scores for a specific judge across all categories.
     */
    public function scoresheetDetail(\App\Models\User $judge)
    {
        // Get all categories assigned to this judge
        $categories = $judge->assignedCategories()
            ->with('criteria')
            ->orderBy('order')
            ->get();

        // Get scores for each category
        $categoriesWithScores = $categories->map(function ($category) use ($judge) {
            $scores = \App\Models\Score::with(['contestant', 'criterion'])
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

        return view('tabulator.scoresheet-detail', compact('judge', 'categoriesWithScores'));
    }

    /**
     * Display print view for a specific judge's scoresheets.
     */
    public function scoresheetPrint(\App\Models\User $judge)
    {
        // Get all categories assigned to this judge
        $categories = $judge->assignedCategories()
            ->with('criteria')
            ->orderBy('order')
            ->get();

        // Get scores for each category
        $categoriesWithScores = $categories->map(function ($category) use ($judge) {
            $scores = \App\Models\Score::with(['contestant', 'criterion'])
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

        return view('tabulator.scoresheet-print', compact('judge', 'categoriesWithScores'));
    }
}
