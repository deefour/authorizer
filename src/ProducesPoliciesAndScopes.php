<?php

namespace Deefour\Authorizer;

use Deefour\Producer\ProducesClasses;
use Deefour\Authorizer\Contracts\Authorizee;

/**
 * For use within a class that should be able to resolve a policy and scope based
 * on it's own class name.
 */
trait ProducesPoliciesAndScopes
{
    use ProducesClasses;

    /**
     * @inheritdoc
     */
    public function policy(Authorizee $authorizee, $policy = null)
    {
        if (is_null($policy)) {
          return (new Authorizer($authorizee))->policy($this);
        }

        if (is_a($policy, Policy::class, true)) {
          return new $policy($authorizee, $this);
        }

        throw new NotAuthorizableException($this, $policy);
    }

    /**
     * @inheritdoc
     */
    public function scope(Authorizee $authorizee, $scope = null)
    {
        if (is_null($scope)) {
          return (new Authorizer($authorizee))->scope($this);
        }

        if (is_a($scope, Scope::class, true)) {
          return new $scope($authorizee, $this->baseScope());
        }

        throw new NotScopeableException($this, $scope);
    }
}
