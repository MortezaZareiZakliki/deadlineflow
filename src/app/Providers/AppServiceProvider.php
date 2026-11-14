<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Repositories\Eloquent\EloquentTaskRepository;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Repositories\Eloquent\EloquentProjectRepository;
use App\Repositories\Contracts\VersionRepositoryInterface;
use App\Repositories\Eloquent\EloquentVersionRepository;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TaskRepositoryInterface::class,    EloquentTaskRepository::class);
        $this->app->bind(ProjectRepositoryInterface::class, EloquentProjectRepository::class);
        $this->app->bind(VersionRepositoryInterface::class, EloquentVersionRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Password::defaults(function () {
            return Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised();
        });
    }
}
