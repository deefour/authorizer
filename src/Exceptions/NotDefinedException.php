<?php namespace Deefour\Authorizer\Exceptions;

/**
 * When thrown, the derived class name for the policy or scope class requested
 * for an object does not actually exist within the application's load path.
 */
class NotDefinedException extends Exception {
}
