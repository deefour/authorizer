<?php namespace Deefour\Authorizer\Exceptions;

use Deefour\Authorizer\Contracts\Authorizable;
use Deefour\Authorizer\Policy;

/**
 * When thrown, the current user is not allowed to perform the requested action
 * against the object bound to the policy.
 *
 * This is the generic "sorry, you can't do that!" exception.
 */
class NotAuthorizedException extends Exception {

  /**
   * The name of the action being authorized.
   *
   * @var string
   */
  public $action;

  /**
   * The policy class.
   *
   * @var Policy
   */
  public $policy;

  /**
   * The authorizable record.
   *
   * @var Authorizable
   */
  public $record;

}
