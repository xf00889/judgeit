<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Repository bindings
        $this->app->bind(
            \App\Repositories\ScoreRepositoryInterface::class,
            \App\Repositories\ScoreRepository::class
        );
        $this->app->bind(
            \App\Repositories\CategoryRepositoryInterface::class,
            \App\Repositories\CategoryRepository::class
        );
        $this->app->bind(
            \App\Repositories\ContestantRepositoryInterface::class,
            \App\Repositories\ContestantRepository::class
        );

        // Register Service bindings
        $this->app->bind(
            \App\Services\CategoryServiceInterface::class,
            \App\Services\CategoryService::class
        );
        $this->app->bind(
            \App\Services\ScoringServiceInterface::class,
            \App\Services\ScoringService::class
        );
        $this->app->bind(
            \App\Services\ResultServiceInterface::class,
            \App\Services\ResultService::class
        );
        
        // Register OverallRankingService as singleton
        $this->app->singleton(\App\Services\OverallRankingService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register policies
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Category::class, \App\Policies\CategoryPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Score::class, \App\Policies\ScorePolicy::class);
    }
}
