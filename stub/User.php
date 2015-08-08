<?php

namespace Deefour\Authorizer\Stubs;

use Deefour\Authorizer\Contracts\Authorizee as AuthorizeeContract;
use Deefour\Authorizer\PassthruPolicyAndScopeProduction;

class User extends Model implements AuthorizeeContract
{
  use PassthruPolicyAndScopeProduction;
}
