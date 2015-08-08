<?php

namespace Deefour\Authorizer\Contracts;

use Deefour\Authorizer\Policy;
use Deefour\Authorizer\Scope;
use Deefour\Producer\Contracts\Producer;

interface Authorizable extends Producer
{
    /**
     * Wrap this object in a newly instantiated policy.
     *
     * @param string $policy [optional]
     * @return Policy
     */
    public function policy(Authorizee $authorizee, $policy = null);

    /**
     * Wrap this object in a newly instantiated scope.
     *
     * @param string $scope [optional]
     * @return Scope
     */
    public function scope(Authorizee $authorizee, $scope = null);
}
