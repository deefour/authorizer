<?php

namespace Deefour\Authorizer\Exception;

/**
 * Thrown when checking if authorization has been performed during the lifecycle
 * of the current request.
 *
 * @see ProvidesAuthorization::verifyAuthorized()
 */
class AuthorizationNotPerformedException extends Exception
{
    //
}
