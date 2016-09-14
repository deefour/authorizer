<?php

namespace Deefour\Authorizer\Exception;

/**
 * Throws when an authorization check fails (the policy class in question returned
 * a non-true value for the requested action).
 *
 * @see Authorizer::authorize()
 * @see ProvidesAuthorization::authorize()
 */
class NotAuthorizedException extends Exception
{
    /**
     * The action being requested.
     *
     * @var string
     */
    public $action;

    /**
     * The record authorization is checked against.
     *
     * @var mixed
     */
    public $record;

    /**
     * The policy class for the $record, containing the action.
     *
     * @var mixed
     */
    public $policy;

    /**
     * Constructor.
     *
     * @param  array $options
     */
    public function __construct($options = [])
    {
        if (is_string($options)) {
            $message = $options;
        } else {
            $options = array_merge(
                array_fill_keys(['action', 'record', 'policy', 'message'], null),
                $options
            );

            $this->action  = $options['action'];
            $this->record = $options['record'];
            $this->policy = $options['policy'];

            $recordName = is_object($this->record) ? get_class($this->record) : $this->record;

            $default    = sprintf('Not allowed to %s this %s', $this->action, $recordName);
            $message    = $options['message'] ?: $default;
        }

        parent::__construct($message);
    }
}
