<?php namespace Deefour\Authorizer\Exceptions;

/**
 * When thrown, the current user is not allowed to perform the requested action
 * against the object bound to the policy.
 *
 * This is the generic "sorry, you can't do that!" exception.
 */
class NotAuthorizedException extends \Exception {}
