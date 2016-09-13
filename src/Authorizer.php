<?php

namespace Deefour\Authorizer;

use Deefour\Authorizer\Exception\NotAuthorizedException;

class Authorizer
{
    /**
     * @api
     */
    public function authorize($user, $record, $query)
    {
        $policy = $this->policyOrFail($user, $record);
        $result = $policy->$query();
        $options = array_merge(compact('query', 'record', 'policy'), [ 'message' => $result ]);

        if ($result !== true) {
            throw new NotAuthorizedException($options);
        }

        return $record;
    }

    /**
     * @api
     */
    public function scope($user, $scope)
    {
        $scope = (new Resolver($scope))->scope();

        if ($scope) {
            return (new $scope($user, $scope))->resolve();
        }
    }

    /**
     * @api
     */
    public function scopeOrFail($user, $scope)
    {
        $scope = (new Resolver($scope))->scopeOrFail();

        return (new $scope($user, $scope))->resolve();
    }

    /**
     * @api
     */
    public function policy($user, $record)
    {
        $policy = (new Resolver($record))->policy();

        if ($policy) {
            return new $policy($user, $record);
        }
    }

    /**
     * @api
     */
    public function policyOrFail($user, $record)
    {
        $policy = (new Resolver($record))->policyOrFail();

        return new $policy($user, $record);
    }
}
