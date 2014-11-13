<?php namespace Deefour\Authorizer\Providers;

use Illuminate\Support\ServiceProvider;

class AuthorizationServiceProvider extends ServiceProvider {

  /**
   * Indicates if loading of the provider is deferred.
   *
   * @var bool
   */
  protected $defer = true;



  /**
   * Register the service provider.
   *
   * @return void
   */
  public function register() {
    $this->app->bindShared('authorizer', function() {
      $config = $this->app['config'];

      if ( ! $config->has('authorizer.user')) {
        throw new \LogicException('A \'authorizer.user\' must be defined in the application config.');
      }

      $user   = $config->get('authorizer.user');
      $config = $config->get('authorizer');

      // The `user` option can be a Closure. If it is, get the return value
      if (is_callable($user)) {
        $user = call_user_func($user);
      }

      return new Policy($user, $config);
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
