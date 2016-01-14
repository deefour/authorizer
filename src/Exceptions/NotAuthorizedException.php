<?php

namespace Deefour\Authorizer\Exceptions;

use Deefour\Authorizer\Contracts\Authorizable;
use Deefour\Authorizer\Policy;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * When thrown, the current user is not allowed to perform the requested action
 * against the object bound to the policy.
 *
 * This is the generic "sorry, you can't do that!" exception.
 */
class NotAuthorizedException extends AccessDeniedHttpException
{
    /**
     * The authorizable record.
     *
     * @var Authorizable
     */
    public $record;

    /**
     * The policy class.
     *
     * @var Policy
     */
    public $policy;

    /**
     * The name of the action being authorized.
     *
     * @var string
     */
    public $action;

    /**
     * The reason why the authorization failed.
     *
     * @var string
     */
    public $reason;

    /**
     * Constructor.
     *
     * {@inheritdoc}
     *
     * @param Authorizable $record
     * @param Policy $policy
     * @param string $action
     * @param string $reason [optional]
     */
    public function __construct(Authorizable $record, Policy $policy, $action, $reason = '')
    {
        $this->record = $record;
        $this->policy = $policy;
        $this->action = $action;
        $this->reason = $reason;

        parent::__construct($this->message());
    }

    /**
     * Format a message for the exception.
     *
     * @return string
     */
    protected function message()
    {
        if ($this->reason) {
            return $this->reason;
        }

        return sprintf(
            'Not allowed to [%s] the [%s] (according to [%s])',
            $this->action,
            get_class($this->record),
            get_class($this->policy)
        );
    }
}
