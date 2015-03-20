<?php namespace Deefour\Authorizer\Stubs;

use Deefour\Authorizer\Contracts\Authorizable;
use Deefour\Authorizer\Contracts\Scopeable;
use Deefour\Authorizer\ResolvesPoliciesAndScopes;

abstract class Model implements Authorizable, Scopeable {

  use ResolvesPoliciesAndScopes;

  /**
   * {@inheritdoc}
   */
  public function baseScope() {
    return 'foo';
  }

}
