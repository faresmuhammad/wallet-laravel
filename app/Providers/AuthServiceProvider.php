<?php

namespace App\Providers;

use App\Models\Currency;
use App\Models\User;
use App\Models\Wallet;
use App\Policies\WalletPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Wallet::class => WalletPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('create-currency', fn(User $user) => $user->isAdmin());
        Gate::define('update-currency', fn(User $user) => $user->isAdmin());
        Gate::define('delete-currency', fn(User $user) => $user->isAdmin());
    }
}
