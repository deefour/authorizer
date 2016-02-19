<?php

namespace Deefour\Authorizer\Stubs;

use Deefour\Authorizer\Contracts\Authorizable;
use Deefour\Authorizer\Contracts\Scopeable;
use Deefour\Authorizer\ProducesPoliciesAndScopes;
use Deefour\Producer\ProducesClasses;

abstract class Model implements Authorizable, Scopeable
{
    use ProducesPoliciesAndScopes, ProducesClasses;

    /**
     * @inheritdoc
     */
    public function resolve($what)
    {
      return get_class($this) . ucfirst($what);
    }

    /**
     * @inheritdoc
     */
    public function baseScope()
    {
        return 'foo';
    }
}
