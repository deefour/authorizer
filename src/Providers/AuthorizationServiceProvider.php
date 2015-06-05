<?php

namespace Deefour\Authorizer\Providers;

use Deefour\Authorizer\Contracts\Authorizee;
use Deefour\Authorizer\Authorizer;
use Illuminate\Support\ServiceProvider;

class AuthorizationServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerAuthorizer();
        $this->registerUserResolver();
    }

    public function registerAuthorizer()
    {
        $this->app->singleton('authorizer', function () {
            return new Authorizer($this->app[Authorizee::class]);
        });
    }

    public function registerUserResolver()
    {
        $this->app->bind(Authorizee::class, function ($app) {
            return $app['auth']->user();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['authorizer'];
    }
}
