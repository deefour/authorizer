<?php namespace Deefour\Authorizer\Providers;

use Deefour\Authorizer\Authorizer;
use Illuminate\Support\ServiceProvider;

class AuthorizationServiceProvider extends ServiceProvider {

  /**
   * Register the service provider.
   *
   * @return void
   */
  public function register() {
    $this->registerAuthorizer();

    $this->registerUserResolver();
  }

  public function registerAuthorizer() {
    $this->app->singleton('authorizer', function() {
      return new Authorizer($this->app['Deefour\Authorizer\Contracts\Authorizee']);
    });
  }

  public function registerUserResolver() {
    $this->app->bind('Deefour\Authorizer\Contracts\Authorizee', function($app) {
      return $app['auth']->user();
    });
  }

  /**
   * Get the services provided by the provider.
   *
   * @return array
   */
  public function provides() {
    return [ 'authorizer' ];
  }

}
