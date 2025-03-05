<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('access-salon-routes', function (User $user) {
            return $user->user_type === 'Salon';
        });
        Gate::define('access-client-routes', function (User $user) {
            return $user->user_type === 'Client';
        });
        Gate::define('access-admin-routes', function (User $user) {
            return $user->user_type === 'Admin';
        });

        Passport::usePersonalAccessClientModel(User::class);
    }
}
