<?php

namespace Deefour\Authorizer;

use Deefour\Authorizer\Exception\NotAuthorizedException;
use Deefour\Transformer\Transformer;

/**
 * Standalone class to perform authorization, resolve policy classes, and scope
 * query objects.
 */
class Authorizer
{
    /**
     * Authorize a $user to perform an $action on a $record.
     *
     * @api
     * @throws \Deefour\Authorizer\Exception\NotAuthorizedException
     * @param  mixed $user
     * @param  mixed $record
     * @param  string $action
     * @return mixed
     */
    public function authorize($user, $record, $action)
    {
        $policy = $this->policyOrFail($user, $record);
        $result = $policy->$action();
        $options = array_merge(compact('query', 'record', 'policy', 'action'), [ 'message' => $result ]);

        if ($result !== true) {
            throw new NotAuthorizedException($options);
        }

        return $record;
    }

    /**
     * Resolve a policy class for the $record, instantiating it with the $user and
     * $record provided.
     *
     * @api
     * @param  mixed $user
     * @param  mixed $record
     * @return mixed
     */
    public function policy($user, $record)
    {
        $policy = (new Resolver($record))->policy();

        if ( ! is_null($policy)) {
            return new $policy($user, $record);
        }
    }

    /**
     * Resolve a restricted version of a scoped query object based on a user's
     * privilege to what is typically an iterable collection of results.
     *
     * @api
     * @param  mixed $user
     * @param  mixed $scope
     * @param  callable|null $lookup
     * @return mixed
     */
    public function scope($user, $scope, callable $lookup = null)
    {
        $record = is_null($lookup) ? $scope : call_user_func($lookup, $scope);
        $scope  = (new Resolver($record))->scope();

        if ( ! is_null($scope)) {
            return (new $scope($user, $scope))->resolve();
        }
    }

    /**
     * Resolve a policy class for the $record. Throw an exception if the policy
     * class can't be resolved.
     *
     * @api
     * @see self::policy()
     * @throws \Deefour\Authorizer\Exception\NotDefinedException
     * @param  mixed $user
     * @param  mixed $record
     * @return mixed
     */
    public function policyOrFail($user, $record)
    {
        $policy = (new Resolver($record))->policyOrFail();

        return new $policy($user, $record);
    }

    /**
     * Resolve a scope class for the $record. Throw an exception if the scope
     * class can't be resolved.
     *
     * @api
     * @see self::scope()
     * @throws \Deefour\Authorizer\Exception\NotDefinedException
     * @param  mixed $user
     * @param  mixed $scope
     * @param  callable|null $lookup
     * @return mixed
     */
    public function scopeOrFail($user, $scope, callable $lookup = null)
    {
        $record = is_null($lookup) ? $scope : call_user_func($lookup, $scope);
        $scope = (new Resolver($record))->scopeOrFail();

        return (new $scope($user, $scope))->resolve();
    }

    /**
     * Filter request input by a policy function that provides a whitelist of
     * attribute names based on a user's privilege over the $record for the
     * $action specified.
     *
     * @api
     * @param  mixed $user
     * @param  mixed $record
     * @param  string|null $action
     * @return array
     */
    public function permittedAttributes($user, $record, $action = null)
    {
        $policy = $this->policyOrFail($user, $record);
        $method = 'permittedAttributesFor' . ucfirst($action);

        if ( ! is_null($action) && method_exists($policy, $method)) {
            return $policy->$method();
        }

        return $policy->permittedAttributes();
    }
}
