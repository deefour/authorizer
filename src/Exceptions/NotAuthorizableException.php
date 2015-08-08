<?php

namespace Deefour\Authorizer\Exceptions;

use Exception;
use Deefour\Authorizer\Contracts\Authorizable;

class NotAuthorizableException extends Exception
{
    /**
     * The object to wrap in a policy.
     *
     * @var Authorizable
     */
    protected $authorizable;

    /**
     * The policy FQCN.
     *
     * @var string
     */
    protected $policy;

    /**
     * Constructor.
     *
     * @param Authorizable $authorizable
     * @param string $policy
     */
    public function __construct(Authorizable $authorizable, $policy) {
        $this->authorizable = $authorizable;
        $this->policy       = $policy;

        parent::__construct($this->message());
    }

    /**
     * Format a message for the exception.
     *
     * @return string
     */
    protected function message() {
        return sprintf(
            'The [%s] object does not implement [%s]. It cannot be used to ' .
            'decorate [%s]',
            $this->policy,
            Authorizable::class,
            get_class($this->authorizable)
        );
    }
}
