<?php

namespace Deefour\Authorizer\Exception;

class NotAuthorizedException extends Exception
{
    public $query;

    public $record;

    public $policy;

    public function __construct($options = [])
    {
        if (is_string($options)) {
            $message = $options;
        } else {
            $options = array_merge(
                array_fill_keys(['query', 'record', 'policy', 'message'], null),
                $options
            );

            $this->query  = $options['query'];
            $this->record = $options['record'];
            $this->policy = $options['policy'];

            $recordName = is_object($this->record) ? get_class($this->record) : $this->record;
            $default    = sprintf('Not allowed to %s this %s', $this->query, $recordName);
            $message    = $options['message'] ?? $default;
        }

        parent::__construct($message);
    }
}
