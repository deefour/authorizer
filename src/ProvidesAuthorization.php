<?php

namespace Deefour\Authorizer;

use InvalidArgumentException;
use Deefour\Authorizer\Contracts\Scopeable;
use Deefour\Authorizer\Contracts\Authorizee;
use Deefour\Authorizer\Contracts\Authorizable;
use Deefour\Producer\Exceptions\NotProducibleException;
use Deefour\Authorizer\Exceptions\NotAuthorizedException;
use Deefour\Authorizer\Exceptions\NotAuthenticatedException;
use Deefour\Authorizer\Exceptions\ScopingNotPerformedException;
use Deefour\Authorizer\Exceptions\AuthorizationNotPerformedException;

/**
 * For use within this package's Authorizer class or in your application's
 * controller(s) or other classes that serve as a point where authorization
 * needs to be performed.
 *
 * @see Authorizer
 */
trait ProvidesAuthorization
{
    /**
     * Wether a request to derive and retrieve a scope class has been made for the
     * current request.
     *
     * @var bool
     */
    protected $_policyScoped = false;

    /**
     * Wether a request to derive and retrieve a policy class has been made for
     * the current request.
     *
     * @var bool
     */
    protected $_policyAuthorized = false;

    /**
     * Authorizes the current user against the passed `$record` for a specific
     * action.
     *
     * If no `$action` is passed, `debug_backtrace` looks back at the name of the
     * caller, using it as the method name to call on the policy class for the
     * authorization check.
     *
     *
     * @throws InvalidArgumentException if the action to call against the policy
     *                                  was not explicitly passed to the
     *                                  `authorize` call and could not be derived
     *                                  from the caller.
     * @throws NotAuthorizedException   if the current user
     *                                  is not authorized for the requested `$action`
     *
     * @param Authorizable $record
     * @return true
     */
    public function authorize(Authorizable $record)
    {
        $className = get_class($record);
        $args      = func_get_args();

        array_shift($args); // shift $record off the stack.

        $action = array_shift($args);

        $this->_policyAuthorized = true;

        if (is_null($action)) {
            $action = debug_backtrace(false)[1]['function'];

            if ($action === 'call_user_func_array' && static::class === debug_backtrace(false)[0]['class']) {
                throw new InvalidArgumentException(sprintf(
                    'No method/action passed to static [%s::authorize()] call.',
                    static::class
                ));
            }
        }

        $policy = $this->policy($record);

        if (!call_user_func_array([$policy, $action], $args)) {
            $exception = new NotAuthorizedException($record, $policy, $action);

            throw $exception;
        }

        return true;
    }

    /**
     * Retrieve a policy for the passed `$record`, throwing an exception if no
     * policy could be found. This is a convenience method for the
     * `getPolicyOrFail` method.
     *
     * @see getPolicyOrFail
     * @throws NotDefinedException
     *
     * @param Authorizable $record
     * @return Policy
     */
    public function policy(Authorizable $record)
    {
        return $this->getPolicyOrFail($this->user(), $record);
    }

    /**
     * Retrieve a modified scope for the passed `$scope`, throwing an exception
     * if no scope could be found. This is a convenience method for the `getScopeOrFail`
     * method.
     *
     * @see getScopeOrFail
     * @throws NotDefinedException
     *
     * @param Scopeable $record
     * @return Scope
     */
    public function scope(Scopeable $record)
    {
        $this->_policyScoped = true;

        return $this->getScopeOrFail($this->user(), $record);
    }

    /**
     * Derive the name for and instantiate an instance of a policy class for the
     * passed `$record` object.
     *
     * @param Authorizee   $user
     * @param Authorizable $record
     * @return Policy|null
     */
    public function getPolicy(Authorizee $user, Authorizable $record)
    {
        try {
            return $this->getPolicyOrFail($user, $record);
        } catch (NotProducibleException $e) {
            return null;
        }
    }

    /**
     * Derive the name for and instantiate an instance of a scope class for the
     * passed `$scope` object. The `$user` will be used to conditionally modify
     * the scope.
     *
     * @param Authorizee $user
     * @param Scopeable  $record
     * @return Scope|null
     */
    public function getScope(Authorizee $user, Scopeable $record)
    {
        try {
            return $this->getScopeOrFail($user, $record);
        } catch (NotProducibleException $e) {
            return null;
        }
    }

    /**
     * Retrieve a policy for the passed `$record`, throwing an exception if no
     * policy could be found.
     *
     * @see getPolicyOrFail
     * @throws NotProducibleException
     *
     * @param Authorizee   $user
     * @param Authorizable $record
     * @return Policy
     */
    public function getPolicyOrFail(Authorizee $user, Authorizable $record)
    {
        return (new Producer($record, $user))->produce('policy');
    }

    /**
     * Retrieve a modified scope for the passed `$scope`, throwing an exception
     * if no scope could be found.
     *
     * @throws NotProducibleException
     *
     * @param Authorizee $user
     * @param Scopeable  $record
     * @return Scope
     */
    public function getScopeOrFail(Authorizee $user, Scopeable $record)
    {
        return (new Producer($record, $user))->produce('scope');
    }

    /**
     * Throws an exception if authorization has not been performed when called.
     * This is typically used as a guard against requests which have yet to be
     * guarded by Aide's authorization, called in some sort of middleware.
     *
     * @throws AuthorizationNotPerformedException
     * @return void
     */
    public function verifyAuthorized()
    {
        if (!$this->_policyAuthorized) {
            throw new AuthorizationNotPerformedException();
        }
    }

    /**
     * Throws an exception if the request has not made a request to resolve a
     * scope. This is typically used as a guard against requests without proper
     * scoping, called in some sort of middleware, preventing record data from
     * being accidentally displayed to a user.
     *
     * @throws ScopingNotPerformedException
     * @return void
     */
    public function verifyScoped()
    {
        if (!$this->_policyScoped) {
            throw new ScopingNotPerformedException();
        }
    }

    /**
     * Skip the requirement for authorization to be performed.
     *
     * @return void
     */
    public function skipAuthorization()
    {
        $this->_policyAuthorized = true;
    }

    /**
     * Skip the requirement for scoping to be performed.
     *
     * @return void
     */
    public function skipScoping()
    {
        $this->_policyScoped = true;
    }

    /**
     * Returns an object representing the user being used for authorization.
     *
     * @return Authorizee
     */
    abstract protected function user();
}
