<?php namespace Deefour\Authorizer;

use Deefour\Authorizer\Contracts\Authorizable;
use Deefour\Authorizer\Contracts\Authorizee;
use Deefour\Authorizer\Contracts\Scopeable;
use Deefour\Authorizer\Exceptions\AuthorizationNotPerformedException;
use Deefour\Authorizer\Exceptions\NotAuthorizedException;
use Deefour\Authorizer\Exceptions\ScopingNotPerformedException;
use InvalidArgumentException;

trait ProvidesAuthorization {

  /**
   * Wether a request to derive and retrieve a scope class has been made for the
   * current request
   *
   * @protected
   * @var boolean
   */
  protected $_policyScoped = false;

  /**
   * Wether a request to derive and retrieve a policy class has been made for the
   * current request
   *
   * @protected
   * @var boolean
   */
  protected $_policyAuthorized = false;



  /**
   * Derive the name for and instantiate an instance of a scope class for the passed
   * `$scope` object. The `$user` will be used to conditionally modify the scope.
   *
   * @param  Authorizee  $user
   * @param  Scopeable  $scope
   * @return Scope|null
   */
  protected static function getScope(Authorizee $user, Scopeable $scope) {
    $policyScope = (new Finder($scope))->scope();

    return $policyScope ? (new $policyScope($user, $scope->baseScope()))->resolve() : null;
  }

  /**
   * Derive the name for and instantiate an instance of a policy class for the passed
   * `$record` object.
   *
   * @param  Authorizee  $user
   * @param  Authorizable  $record
   * @return Policy|null
   */
  protected static function getPolicy(Authorizee $user, Authorizeable $record) {
    $policy = (new Finder($record))->policy();

    return $policy ? new $policy($user, $record) : null;
  }

  /**
   * Retrieve a modified scope for the passed `$scope`, throwing an exception if no scope
   * could be found.
   *
   * @throws  NotDefinedException
   * @param  Authorizee  $user
   * @param  Scopeable  $scope
   * @return Scope
   */
  protected static function getScopeOrFail(Authorizee $user, Scopeable $scope) {
    $policyScope = (new Finder($scope))->scopeOrFail();

    return (new $policyScope($user, $scope->baseScope()))->resolve();
  }

  /**
   * Retrieve a policy for the passed `$record`, throwing an exception if no policy
   * could be found.
   *
   * @protected
   * @see    getPolicyOrFail
   * @throws  NotDefinedException
   * @param  Authorizee  $user
   * @param  Authorizable  $record
   * @return Policy
   */
  protected static function getPolicyOrFail(Authorizee $user, Authorizable $record) {
    $policy = (new Finder($record))->policyOrFail();

    return new $policy($user, $record);
  }



  /**
   * Throws an exception if authorization has not been performed when called. This
   * is typically used as a guard against requests which have yet to be guarded by
   * Aide's authorization, called in some sort of middleware.
   *
   * @protected
   * @throws  AuthorizationNotPerformedException
   */
  protected function verifyAuthorized() {
    if ( ! $this->_policyAuthorized) {
      throw new AuthorizationNotPerformedException;
    }
  }

  /**
   * Throws an exception if the request has not made a request to resolve a scope.
   * This is typically used as a guard against requests without proper scoping,
   * called in some sort of middleware, preventing record data from being accidentally
   * displayed to a user.
   *
   * @protected
   * @throws  ScopingNotPerformedException
   */
  protected function verifyPolicyScoped() {
    if ( ! $this->_policyScoped) {
      throw new ScopingNotPerformedException;
    }
  }

  /**
   * Authorizes the current user against the passed `$record` for a specific action.
   *
   * If no `$action` is passed, `debug_backtrace` looks back at the name of the
   * caller, using it as the method name to call on the policy class for the
   * authorization check.
   *
   * @protected
   * @param  Authorizable   $record
   * @param  string  $action  [optional]
   * @throws InvalidArgumentException if the action to call against the policy was
   *         not explicitly passed to the `authorize` call and could not be derived
   *         from the caller.
   * @throws  NotAuthorizedException if the current user
   *         is not authorized for the requested `$action`
   * @return true
   */
  protected function authorize(Authorizable $record, $action = null) {
    $className = get_class($record);

    $this->_policyAuthorized = true;

    if (is_null($action)) {
      $action = debug_backtrace(false)[1]['function'];

      if ($action === 'call_user_func_array' and static::class === debug_backtrace(false)[0]['class']) {
        throw new InvalidArgumentException(sprintf('No method/action passed to static `%s::authorize()` call.', static::class));
      }
    }

    $policy = $this->policy($record);

    if ( ! $policy->$action()) {
      $exception = new NotAuthorizedException("Not allowed to `${action}` this `${className}`");

      $exception->action = $action;
      $exception->policy = $policy;
      $exception->record = $record;

      throw $exception;
    }

    return true;
  }

  /**
   * Retrieve a modified scope for the passed `$scope`, throwing an exception if no scope
   * could be found. This is a convenience method for the `getScopeOrFail` method.
   *
   * @protected
   * @see    getScopeOrFail
   * @throws  NotDefinedException
   * @param  Scopeable  $scope
   * @return Scope
   */
  protected function scope(Scopeable $scope) {
    $this->_policyScoped = true;

    return static::getScopeOrFail($this->authorizee(), $scope);
  }

  /**
   * Retrieve a policy for the passed `$record`, throwing an exception if no policy
   * could be found. This is a convenience method for the `getPolicyOrFail` method.
   *
   * @protected
   * @see    getPolicyOrFail
   * @throws  NotDefinedException
   * @param  Authorizable  $record
   * @return Policy
   */
  protected function policy(Authorizable $record) {
    return static::getPolicyOrFail($this->authorizee(), $record);
  }

  /**
   * Returns an object representing the user being authorized against the resource.
   *
   * This gracefully fails if an object NOT implementing the Authorizee contract
   * is returned.
   *
   * @return [type] [description]
   */
  protected function authorizee() {
    $authorizee = $this->currentUser();

    if ( ! ($authorizee instanceof Authorizee)) {
      throw new NotAuthorizedException(
        'A valid authorizee was not provided. This often means no user is logged in.'
      );
    }

    return $authorizee;
  }



  /**
   * Returns an object representing the user being used for authorization.
   *
   * @abstract
   * @protected
   * @return mixed
   */
  abstract protected function currentUser();

}
