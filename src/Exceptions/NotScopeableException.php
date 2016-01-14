<?php

namespace Deefour\Authorizer\Exceptions;

use Exception;
use Deefour\Authorizer\Contracts\Scopeable;

class NotScopeableException extends Exception
{
    /**
     * The object to wrap in a scope.
     *
     * @var Scopeable
     */
    protected $scopeable;

    /**
     * The scope FQCN.
     *
     * @var string
     */
    protected $scope;

    /**
     * Constructor.
     *
     * @param Scopeable $scopeable
     * @param string $scope
     */
    public function __construct(Scopeable $scopeable, $scope)
    {
        $this->scopeable = $scopeable;
        $this->scope     = $scope;

        parent::__construct($this->message());
    }

    /**
     * Format a message for the exception.
     *
     * @return string
     */
    protected function message()
    {
        return sprintf(
            'The [%s] object does not implement [%s]. It cannot be used to ' .
            'decorate [%s]',
            $this->scope,
            Scopeable::class,
            get_class($this->scopeable)
        );
    }
}
