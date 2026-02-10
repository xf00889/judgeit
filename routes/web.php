<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    
    Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])
        ->middleware('throttle:5,1') // 5 attempts per minute
        ->name('login.post');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])
        ->name('logout');
    
    // Dashboard redirect based on role
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isJudge()) {
            return redirect()->route('judge.index');
        } elseif ($user->isTabulator()) {
            return redirect()->route('tabulator.results');
        }
        
        return redirect()->route('home');
    })->name('dashboard');
    
    // Admin routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');
        
        // User Management
        Route::resource('users', App\Http\Controllers\Admin\UserController::class);
        Route::get('users/{user}/assign-categories', [App\Http\Controllers\Admin\UserController::class, 'assignCategories'])
            ->name('users.assign-categories');
        Route::post('users/{user}/assign-categories', [App\Http\Controllers\Admin\UserController::class, 'updateCategoryAssignments'])
            ->name('users.update-categories');
        Route::post('users/{user}/judging-gender', [App\Http\Controllers\Admin\UserController::class, 'updateJudgingGender'])
            ->name('users.update-judging-gender');
        
        // Contestant Management
        Route::resource('contestants', App\Http\Controllers\Admin\ContestantController::class);
        
        // Category Management
        Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class)->except(['destroy']);
        Route::post('categories/{category}/activate', [App\Http\Controllers\Admin\CategoryController::class, 'activate'])
            ->name('categories.activate');
        Route::post('categories/{category}/deactivate', [App\Http\Controllers\Admin\CategoryController::class, 'deactivate'])
            ->name('categories.deactivate');
        
        // Overall Rankings Management
        Route::resource('overall-rankings', App\Http\Controllers\Admin\OverallRankingController::class);
        Route::get('overall-rankings/{overallRanking}/results', [App\Http\Controllers\Admin\OverallRankingController::class, 'show'])
            ->name('overall-rankings.results');
        Route::post('overall-rankings/{overallRanking}/activate', [App\Http\Controllers\Admin\OverallRankingController::class, 'activate'])
            ->name('overall-rankings.activate');
        Route::post('overall-rankings/{overallRanking}/deactivate', [App\Http\Controllers\Admin\OverallRankingController::class, 'deactivate'])
            ->name('overall-rankings.deactivate');
        
        // Fixed Scores Management
        Route::get('overall-rankings/{overallRanking}/fixed-scores', [App\Http\Controllers\Admin\OverallRankingController::class, 'manageFixedScores'])
            ->name('overall-rankings.fixed-scores');
        Route::post('overall-rankings/{overallRanking}/fixed-scores', [App\Http\Controllers\Admin\OverallRankingController::class, 'saveFixedScores'])
            ->name('overall-rankings.fixed-scores.save');
        
        // Criteria Management (nested under categories)
        Route::prefix('categories/{category}')->name('criteria.')->group(function () {
            Route::get('criteria', [App\Http\Controllers\Admin\CriterionController::class, 'index'])
                ->name('index');
            Route::get('criteria/create', [App\Http\Controllers\Admin\CriterionController::class, 'create'])
                ->name('create');
            Route::post('criteria', [App\Http\Controllers\Admin\CriterionController::class, 'store'])
                ->name('store');
            Route::get('criteria/{criterion}/edit', [App\Http\Controllers\Admin\CriterionController::class, 'edit'])
                ->name('edit');
            Route::put('criteria/{criterion}', [App\Http\Controllers\Admin\CriterionController::class, 'update'])
                ->name('update');
            Route::delete('criteria/{criterion}', [App\Http\Controllers\Admin\CriterionController::class, 'destroy'])
                ->name('destroy');
        });
        
        // Judge Assignment (nested under categories)
        Route::prefix('categories/{category}')->name('judges.')->group(function () {
            Route::get('judges', [App\Http\Controllers\Admin\JudgeAssignmentController::class, 'index'])
                ->name('index');
            Route::post('judges', [App\Http\Controllers\Admin\JudgeAssignmentController::class, 'store'])
                ->name('store');
            Route::delete('judges/{judge}', [App\Http\Controllers\Admin\JudgeAssignmentController::class, 'destroy'])
                ->name('destroy');
        });
        
        // Scoresheet Submissions
        Route::get('scoresheets', [App\Http\Controllers\Admin\ScoresheetController::class, 'index'])
            ->name('scoresheets.index');
        Route::get('scoresheets/{judge}', [App\Http\Controllers\Admin\ScoresheetController::class, 'show'])
            ->name('scoresheets.show');
        Route::get('scoresheets/{judge}/print', [App\Http\Controllers\Admin\ScoresheetController::class, 'print'])
            ->name('scoresheets.print');
    });
    
    // Judge routes
    Route::middleware('role:judge')->prefix('judge')->name('judge.')->group(function () {
        Route::get('/', function () {
            return redirect()->route('judge.index');
        });
        Route::get('/categories', [App\Http\Controllers\Judge\JudgeController::class, 'index'])
            ->name('index');
        Route::get('/categories/{category}', [App\Http\Controllers\Judge\JudgeController::class, 'show'])
            ->name('categories.show');
        Route::post('/categories/{category}/submit', [App\Http\Controllers\Judge\JudgeController::class, 'submitScores'])
            ->name('categories.submit');
    });
    
    // Tabulator routes
    Route::middleware('role:tabulator')->prefix('tabulator')->name('tabulator.')->group(function () {
        Route::get('/', function () {
            return redirect()->route('tabulator.results');
        });
        Route::get('/results', [App\Http\Controllers\Tabulator\TabulatorController::class, 'index'])
            ->name('results');
        Route::get('/results/print', [App\Http\Controllers\Tabulator\TabulatorController::class, 'printResults'])
            ->name('results.print');
        Route::get('/rankings', [App\Http\Controllers\Tabulator\TabulatorController::class, 'rankings'])
            ->name('rankings');
        Route::get('/awards', [App\Http\Controllers\Tabulator\TabulatorController::class, 'awards'])
            ->name('awards');
        Route::get('/awards/print', [App\Http\Controllers\Tabulator\TabulatorController::class, 'printAwards'])
            ->name('awards.print');
        Route::get('/rankings/print', [App\Http\Controllers\Tabulator\TabulatorController::class, 'printRankings'])
            ->name('rankings.print');
        Route::get('/categories/{category}/results', [App\Http\Controllers\Tabulator\TabulatorController::class, 'categoryResults'])
            ->name('categories.results');
        Route::get('/categories/{category}/results/print', [App\Http\Controllers\Tabulator\TabulatorController::class, 'printCategoryResults'])
            ->name('categories.results.print');
        
        // Overall Rankings Management
        Route::get('/overall-rankings', [App\Http\Controllers\Tabulator\TabulatorController::class, 'overallRankingsIndex'])
            ->name('overall-rankings.index');
        Route::get('/overall-rankings/create', [App\Http\Controllers\Tabulator\TabulatorController::class, 'createOverallRanking'])
            ->name('overall-rankings.create');
        Route::post('/overall-rankings', [App\Http\Controllers\Tabulator\TabulatorController::class, 'storeOverallRanking'])
            ->name('overall-rankings.store');
        Route::get('/overall-rankings/{overallRanking}', [App\Http\Controllers\Tabulator\TabulatorController::class, 'showOverallRanking'])
            ->name('overall-rankings.show');
        Route::get('/overall-rankings/{overallRanking}/edit', [App\Http\Controllers\Tabulator\TabulatorController::class, 'editOverallRanking'])
            ->name('overall-rankings.edit');
        Route::put('/overall-rankings/{overallRanking}', [App\Http\Controllers\Tabulator\TabulatorController::class, 'updateOverallRanking'])
            ->name('overall-rankings.update');
        Route::post('/overall-rankings/{overallRanking}/activate', [App\Http\Controllers\Tabulator\TabulatorController::class, 'activateOverallRanking'])
            ->name('overall-rankings.activate');
        Route::post('/overall-rankings/{overallRanking}/deactivate', [App\Http\Controllers\Tabulator\TabulatorController::class, 'deactivateOverallRanking'])
            ->name('overall-rankings.deactivate');
        
        // Fixed Scores Management
        Route::get('/overall-rankings/{overallRanking}/fixed-scores', [App\Http\Controllers\Tabulator\TabulatorController::class, 'manageFixedScores'])
            ->name('overall-rankings.fixed-scores');
        Route::post('/overall-rankings/{overallRanking}/fixed-scores', [App\Http\Controllers\Tabulator\TabulatorController::class, 'saveFixedScores'])
            ->name('overall-rankings.fixed-scores.save');
        
        // Scoresheet Submissions
        Route::get('/scoresheets', [App\Http\Controllers\Tabulator\TabulatorController::class, 'scoresheets'])
            ->name('scoresheets');
        Route::get('/scoresheets/{judge}', [App\Http\Controllers\Tabulator\TabulatorController::class, 'scoresheetDetail'])
            ->name('scoresheets.detail');
        Route::get('/scoresheets/{judge}/print', [App\Http\Controllers\Tabulator\TabulatorController::class, 'scoresheetPrint'])
            ->name('scoresheets.print');
        
        // Finalist Selection (accessible by both admin and tabulator)
        Route::get('/finalists', [App\Http\Controllers\Tabulator\FinalistController::class, 'index'])
            ->name('finalists.index')
            ->withoutMiddleware('role:tabulator')
            ->middleware('role:admin,tabulator');
        Route::post('/finalists/select', [App\Http\Controllers\Tabulator\FinalistController::class, 'select'])
            ->name('finalists.select')
            ->withoutMiddleware('role:tabulator')
            ->middleware('role:admin,tabulator');
        Route::post('/finalists/finalize', [App\Http\Controllers\Tabulator\FinalistController::class, 'finalize'])
            ->name('finalists.finalize')
            ->withoutMiddleware('role:tabulator')
            ->middleware('role:admin,tabulator');
        Route::get('/finalists/print', [App\Http\Controllers\Tabulator\FinalistController::class, 'print'])
            ->name('finalists.print')
            ->withoutMiddleware('role:tabulator')
            ->middleware('role:admin,tabulator');
        Route::delete('/finalists/reset', [App\Http\Controllers\Tabulator\FinalistController::class, 'reset'])
            ->name('finalists.reset')
            ->withoutMiddleware('role:tabulator')
            ->middleware('role:admin,tabulator');
    });
});

