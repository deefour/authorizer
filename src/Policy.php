<?php

namespace Deefour\Authorizer;

use BadMethodCallException;
use Deefour\Authorizer\Contracts\Authorizee;
use Deefour\Authorizer\Contracts\Authorizable;
use Deefour\Producer\Contracts\Producible;

/**
 * Base policy class all application policies are encouraged to extend. Aide
 * expects to pass a user as the first argument to a new policy and the record
 * to authorize against as the second argument.
 */
abstract class Policy implements Producible
{
    /**
     * The user to be authorized.
     *
     * @var Authorizee
     */
    protected $user;

    /**
     * The record/object to authorize against.
     *
     * @var Authorizable
     */
    protected $record;

    /**
     * Sets expectations for dependencies on the policy class and stores
     * references to them locally.
     *
     * @param Authorizee $user
     * @param Authorizable $record
     */
    public function __construct(Authorizee $user, Authorizable $record)
    {
        $this->user   = $user;
        $this->record = $record;
    }

    /**
     * Convenience method to call a policy action by passing the action name as a
     * string. This is particularly handy within the context of a view, making
     * the authorization check a bit more human-readable. For example, within a
     * Laravel blade template:.
     *
     * @if (policy($article)->can('edit'))
     *   // ...
     * @endif
     *
     * @return bool
     */
    public function can($action)
    {
        $args = func_get_args();

        // pop the action off the stack
        array_shift($args);

        return call_user_func_array([$this, $action], $args);
    }

    /**
     * A whitelist of attribute names on the record which should be considered
     * safe for mass assignment.
     *
     * If you need to pass arguments for more complex logic, use func_get_args().
     *
     * @return array
     */
    public function permittedAttributes()
    {
        return [];
    }

    /**
     * Handle calls to non-existant actions with a more informative message.
     *
     * @param string $method
     * @param array $args
     */
    public function __call($method, $args)
    {
        throw new BadMethodCallException(sprintf(
            'No [%s] method exists on [%s]',
            $method,
            get_class($this)
        ));
    }
}
