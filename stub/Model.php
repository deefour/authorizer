<?php namespace Deefour\Authorizer\Stubs;

use Deefour\Authorizer\Contracts\Authorizable as AuthorizableContract;
use Deefour\Authorizer\Authorizable;

abstract class Model implements AuthorizableContract {

  use Authorizable;

}
