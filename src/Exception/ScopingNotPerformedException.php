<?php

namespace Deefour\Authorizer\Exception;

/**
 * Thrown when checking if any query object has been scoped during the lifecycle
 * of the current request.
 *
 * @see ProvidesAuthorization::verifyScoped()
 */
class ScopingNotPerformedException extends Exception
{
    //
}
