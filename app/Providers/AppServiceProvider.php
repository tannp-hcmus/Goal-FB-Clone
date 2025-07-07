<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            'App\Domain\Interfaces\Repositories\UserRepositoryInterface',
            'App\Infrastructure\Repositories\EloquentUserRepository'
        );
        $this->app->bind(
            'App\Domain\Interfaces\Services\UserServiceInterface',
            'App\Application\Services\UserService'
        );
        $this->app->bind(
            'App\Domain\Interfaces\Services\AuthenticationServiceInterface',
            'App\Application\Services\AuthenticationService'
        );
        $this->app->bind(
            'App\Domain\Interfaces\Services\UserProfileServiceInterface',
            'App\Application\Services\UserProfileService'
        );
        $this->app->bind(
            'App\Domain\Interfaces\Repositories\PostRepositoryInterface',
            'App\Infrastructure\Repositories\EloquentPostRepository'
        );
        $this->app->bind(
            'App\Domain\Interfaces\Repositories\CommentRepositoryInterface',
            'App\Infrastructure\Repositories\EloquentCommentRepository'
        );
        $this->app->bind(
            'App\Domain\Interfaces\Repositories\LikeRepositoryInterface',
            'App\Infrastructure\Repositories\EloquentLikeRepository'
        );
        $this->app->bind(
            'App\Domain\Interfaces\Services\FileStorageServiceInterface',
            'App\Application\Services\FileStorageService'
        );
        $this->app->bind(
            'App\Domain\Interfaces\Services\ElasticSearchServiceInterface',
            'App\Application\Services\ElasticSearchService'
        );
        $this->app->bind(
            'App\Domain\Interfaces\Services\UserSearchServiceInterface',
            'App\Application\Services\UserSearchService'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
