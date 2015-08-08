<?php

namespace Deefour\Authorizer\Contracts;

use Deefour\Authorizer\Policy;
use Deefour\Authorizer\Scope;

interface Authorizee
{
    /**
     * Generate a policy for the authorizable, using the this class instance
     * as the authorizee.
     *
     * @param Authorizable $authorizable
     * @return Policy
     */
    public function policyFor(Authorizable $authorizable);

    /**
     * Generate a scope for the authorizable, using the this class instance
     * as the authorizee.
     *
     * @param Authorizable $authorizable
     * @return Scope
     */
    public function scopeFor(Authorizable $authorizable);
}
