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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
