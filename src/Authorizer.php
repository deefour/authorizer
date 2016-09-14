<?php

namespace Deefour\Authorizer;

use Deefour\Authorizer\Exception\NotAuthorizedException;

class Authorizer
{
    /**
     * Authorize a $user to perform an $action on a $record.
     *
     * @api
     * @throws NotAuthorizedException
     * @param  mixed $user
     * @param  mixed $record
     * @param  string $action
     * @return mixed
     */
    public function authorize($user, $record, $action)
    {
        $policy = $this->policyOrFail($user, $record);
        $result = $policy->$action();
        $options = array_merge(compact('query', 'record', 'policy'), [ 'message' => $result ]);

        if ($result !== true) {
            throw new NotAuthorizedException($options);
        }

        return $record;
    }

    /**
     * @api
     * @param  mixed $user
     * @param  mixed $record
     * @return mixed
     */
    public function policy($user, $record)
    {
        $policy = (new Resolver($record))->policy();

        if ($policy) {
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
     * @return mixed
     */
    public function scope($user, $scope)
    {
        $scope = (new Resolver($scope))->scope();

        if ($scope) {
            return (new $scope($user, $scope))->resolve();
        }
    }

    /**
     * Resolve a policy class for the $record. Throw an exception if the policy
     * class can't be resolved.
     *
     * @api
     * @see self::policy()
     * @throws NotDefinedException
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
     * Resolve a scope class for the $record. Throw an exception if the policy
     * class can't be resolved.
     *
     * @api
     * @see self::scope()
     * @return mixed
     */
    public function scopeOrFail($user, $scope)
    {
        $scope = (new Resolver($scope))->scopeOrFail();

        return (new $scope($user, $scope))->resolve();
    }
}
