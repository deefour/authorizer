<?php namespace Deefour\Authorizer\Exceptions;

/**
 * When thrown, no attempt to resolve a scope was performed for the current request.
 *
 * This is typically used within an `after` filter of a controller action or other
 * middleware to help prevent unwanted data from being displayed to a user.
 */
class ScopingNotPerformedException extends \Exception { }
