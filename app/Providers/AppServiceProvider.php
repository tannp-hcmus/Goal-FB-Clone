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
            'App\Application\Interfaces\Repositories\UserRepositoryInterface',
            'App\Infrastructure\Repositories\EloquentUserRepository'
        );
        $this->app->bind(
            'App\Application\Interfaces\Services\UserServiceInterface',
            'App\Domain\Services\UserService'
        );
        $this->app->bind(
            'App\Application\Interfaces\Services\AuthenticationServiceInterface',
            'App\Domain\Services\AuthenticationService'
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
