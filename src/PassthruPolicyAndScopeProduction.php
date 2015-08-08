<?php

namespace Deefour\Authorizer;

use Deefour\Authorizer\Contracts\Authorizable;

/**
 * Implementation of the Authorizee contract, providing a sort of reverse policy
 * and scope resolution, starting from the authorizee instead of an authorizable.
 */
trait PassthruPolicyAndScopeProduction
{
    /**
     * {@inheritdoc}
     */
    public function policyFor(Authorizable $authorizable)
    {
        return $authorizable->policy($this);
    }

    /**
     * {@inheritdoc}
     */
    public function scopeFor(Authorizable $authorizable)
    {
        return $authorizable->scope($this);
    }
}
