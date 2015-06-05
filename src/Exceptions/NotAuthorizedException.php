<?php

namespace Deefour\Authorizer\Exceptions;

use Deefour\Authorizer\Contracts\Authorizable;
use Deefour\Authorizer\Policy;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * When thrown, the current user is not allowed to perform the requested action
 * against the object bound to the policy.
 *
 * This is the generic "sorry, you can't do that!" exception.
 */
class NotAuthorizedException extends UnauthorizedHttpException
{
    /**
     * Constructor.
     *
     * {@inheritdoc}
     */
    public function __construct($message)
    {
        parent::__construct(null, $message);
    }

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
