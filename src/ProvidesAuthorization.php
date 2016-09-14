<?php

namespace Deefour\Authorizer;

use BadMethodCallException;
use Deefour\Authorizer\Exception\AuthorizationNotPerformedException;
use Deefour\Authorizer\Exception\NotAuthorizedException;
use Deefour\Authorizer\Exception\ScopingNotPerformedException;
use Deefour\Transformer\Transformer;

/**
 * Trait providing the ability to perform authorization, resolve policy classes,
 * and scope query objects.
 */
trait ProvidesAuthorization
{
    /**
     * Flag stating whether an attempt to authorize a user against any record has
     * been performed during the request lifecycle.
     *
     * @var bool
     */
    protected $authorizerAuthorized = false;

    /**
     * Flag stating whether an attempt to scope any query object via a scope has
     * been performed during the request lifecycle.
     *
     * @var bool
     */
    protected $authorizerScoped = false;

    /**
     * Cache of previously resolved policy classes.
     *
     * @var array
     */
    protected $authorizerPolicies = [];

    /**
     * Cache of previously resolved scope objects.
     *
     * @var array
     */
    protected $authorizerScopes = [];

    /**
     * Authorize a user to perform an $action on a $record.
     *
     * If an $action is not defined, it will be taken from authorizerAction()
     * just as the user is taken from authorizerUser() for policy resolution.
     *
     * @api
     * @see self::authorizerUser()
     * @see self::authorizerAction()
     * @throws \Deefour\Authorizer\Exception\NotAuthorizedException
     * @param  mixed $record
     * @param  mixed|null $action
     * @return mixed
     */
    public function authorize($record, $action = null)
    {
        $action = $action ?: $this->authorizerAction();

        $this->authorizerAuthorized = true;

        $policy  = $this->policy($record);
        $result  = $policy->$action();
        $options = array_merge(compact('query', 'record', 'policy'), [ 'message' => $result ]);

        if ($result !== true) {
            throw new NotAuthorizedException($options);
        }

        return $record;
    }

    /**
     * Resolve a policy class for the $record.
     *
     * @api
     * @see self::authorizerUser()
     * @param  mixed $record
     * @return mixed
     */
    public function policy($record)
    {
        $hash = is_object($record) ? spl_object_hash($record) : $record;

        if (isset($this->authorizerPolicies[$hash])) {
            return $this->authorizerPolicies[$hash];
        }

        return $this->authorizerPolicies[$hash] = (new Authorizer)->policyOrFail(
            $this->authorizerUser(), $record
        );
    }

    /**
     * Resolve a restricted version of a scoped query object based on a user's
     * privilege to what is typically an iterable collection of results.
     *
     * @api
     * @param  mixed $scope
     * @param  callable|null $lookup
     * @return mixed
     */
    public function scope($scope, callable $lookup = null)
    {
        $this->authorizerScoped = true;

        $record = is_null($lookup) ? $scope : call_user_func($lookup, $scope);
        $hash   = is_object($scope) ? spl_object_hash($record) : $record;

        if (isset($this->authorizerScopes[$hash])) {
            return $this->authorizerScopes[$hash];
        }

        return $this->authorizerScopes[$hash] = (new Authorizer)->scopeOrFail(
            $this->authorizerUser(), $scope, $lookup
        );
    }

    /**
     * Filter request input by a policy function that provides a whitelist of
     * attribute names based on a user's privilege over the $record for the
     * $action specified.
     *
     * @api
     * @see self::authorizerAttributes()
     * @param  mixed $record
     * @param  string|null $action
     * @return array
     */
    public function permittedAttributes($record, $action = null)
    {
        $whitelist = (new Authorizer)->permittedAttributes(
            $this->authorizerUser(), $record, $action
        );

        $attributes = new Transformer($this->authorizerAttributes());

        return $attributes->only($whitelist);
    }

    /**
     * Boolean check if authorization has been performed during the current
     * request's lifecycle.
     *
     * @api
     * @return bool
     */
    public function hasBeenAuthorized()
    {
        return !!$this->authorizerAuthorized;
    }

    /**
     * Boolean check if query object scoping has been performed during the current
     * request's lifecycle.
     *
     * @api
     * @return bool
     */
    public function hasBeenScoped()
    {
        return !!$this->authorizerScoped;
    }

    /**
     * Throw an exception if a an authorization check has not yet been performed.
     *
     * @api
     * @see  self::hasBeenAuthorized()
     * @throws \Deefour\Authorizer\Exception\AuthorizationNotPerformedException
     * @return void
     */
    public function verifyAuthorized()
    {
        if ( ! $this->hasBeenAuthorized()) {
            throw new AuthorizationNotPerformedException;
        }
    }

    /**
     * Throw an exception if scoping for any query object has not yet been performed.
     *
     * @api
     * @see  self::hasBeenScoped()
     * @throws \Deefour\Authorizer\Exception\ScopingNotPerformedException
     * @return void
     */
    public function verifyScoped()
    {
        if ( ! $this->hasBeenScoped()) {
            throw new ScopingNotPerformedException;
        }
    }

    /**
     *
     *
     * @api
     * @return void
     */
    public function skipAuthorization()
    {
        $this->authorizerAuthorized = true;
    }

    /**
     *
     *
     * @api
     * @return void
     */
    public function skipScoping()
    {
        $this->authorizerScoped = true;
    }

    /**
     *
     * @throws \BadMethodCallException
     * @return void
     */
    protected function authorizerAction()
    {
        throw new BadMethodCallException('The authorizerAction method must be defined');
    }

    /**
     *
     * @throws \BadMethodCallException
     * @return void
     */
    protected function authorizerUser()
    {
        throw new BadMethodCallException('The authorizerUser method must be defined');
    }

    /**
     *
     * @throws \BadMethodCallException
     * @return void
     */
    protected function authorizerAttributes()
    {
        throw new BadMethodCallException('The authorizerAttributes method must be defined');
    }
}
