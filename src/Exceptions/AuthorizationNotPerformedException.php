<?php namespace Deefour\Authorizer\Exceptions;

/**
 * When thrown, no attempt to authorize the current user against any policy was
 * performed for the current request.
 *
 * This is typically used within an `after` filter of a controller action or
 * other middleware to help prevent unwanted holes in the applicaiton due to a
 * failure to properly authorize the user against a resource for a specific
 * action.
 */
class AuthorizationNotPerformedException extends Exception {
}
