<?php

namespace Deefour\Authorizer\Stubs;

use Deefour\Authorizer\Contracts\Authorizable;
use Deefour\Authorizer\Contracts\Scopeable;
use Deefour\Authorizer\ProducesPoliciesAndScopes;

abstract class Model implements Authorizable, Scopeable
{
    use ProducesPoliciesAndScopes;

    /**
     * @inheritdoc
     */
    public function baseScope()
    {
        return 'foo';
    }
}
