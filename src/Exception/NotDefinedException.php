<?php

namespace Deefour\Authorizer\Exception;

/**
 * Thrown when the resolver fails to locate a policy or scope class for a passed
 * $record.
 *
 * @see Resolver::scopeOrFail()
 * @see Resolver::policyOrFail()
 */
class NotDefinedException extends Exception
{
    //
}
