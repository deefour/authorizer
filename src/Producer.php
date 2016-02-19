<?php

namespace Deefour\Authorizer;

use Deefour\Producer\ProducesClasses;
use Deefour\Authorizer\Contracts\Authorizee;
use Deefour\Authorizer\Contracts\Authorizable;
use Deefour\Producer\Contracts\Producer as ProducerContract;

class Producer implements ProducerContract
{
    use ProducesClasses;

    /**
     * The user being authorized.
     *
     * @var Authorizable
     */
    protected $user;

    /**
     * The authorizable object.
     *
     * @var Authorizable
     */
    protected $record;

    /**
     * Constructor.
     *
     * @param Authorizable $record
     * @param Authorizee $user
     */
    public function __construct(Authorizable $record, Authorizee $user)
    {
        $this->record = $record;
        $this->user   = $user;
    }

    /**
     * {@inheritdoc}
     *
     * Instantiates an instance of the FQCN passed, injecting the user and record
     * into it.
     *
     * @return Policy|Scope
     */
    public function resolve($what)
    {
        if ( ! method_exists($this->record, 'resolve')) {
            return get_class($this->record) . ucfirst($what);
        }

        return $this->record->resolve($what);
    }

    public function make($producible)
    {
        return new $producible(
            $this->user,
            is_a($producible, Scope::class, true) ? $this->record->baseScope() : $this->record
        );
    }
}
