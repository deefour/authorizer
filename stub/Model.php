<?php namespace Deefour\Authorizer\Stubs;

use Deefour\Authorizer\Contracts\AuthorizableContract;
use Deefour\Authorizer\Traits\Authorizable;

abstract class Model implements AuthorizableContract {

  use Authorizable;

}