<?php

namespace App\Providers;

use App\Services\Auth\GuestGuard;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Auth::provider('sessions', function (Application $app) {
            return new SessionUserProvider(
                $app->make('cache.store')
            );
        });

        Auth::extend('guest', function (Application $app, $name, array $config) {
            return new GuestGuard(Auth::createUserProvider($config['provider']), $app->make('request'));
        //
        });
    }
}
