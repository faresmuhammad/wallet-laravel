<?php

namespace App\Providers;

use App\Services\CreateNewWallet;
use App\Services\DeleteWallet;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->singleton(CreateNewWallet::class);
        $this->app->singleton(DeleteWallet::class);
    }
}
