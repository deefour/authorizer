<?php

use Deefour\Authorizer\Authorizer;
use Deefour\Authorizer\Contracts\Authorizable;
use Deefour\Authorizer\Contracts\Scopeable;
use Deefour\Authorizer\Exceptions\NotScopeableException;
use Illuminate\Database\Eloquent\Builder;

if ( ! function_exists('policy')) {
    /**
     * Retrieve policy class for the passed object. The argument can be a FQCN
     * or an identifier that can be resolved through Laravel's service container.
     *
     * @param Authorizable|string $object
     *
     * @return array
     */
    function policy($object)
    {
        $authorizer = app('authorizer');

        if (is_string($object)) {
            $object = app($object);
        }

        return $authorizer->policy($object);
    }
}

if ( ! function_exists('scope')) {
    /**
     * Retrieve a scoped query for the passed object. The argument can be a FQCN,
     * an identifier that can be resolved through Laravel's service container, or
     * an Eloquent query builder instance.
     *
     * The resulting object must implement the Scopeable interface. If a query builder
     * instance is received, the model class will be fetched from it.
     *
     * If the original object passed is Scopeable, the 'base scope' will be pulled
     * from that object as the root of the query.
     *
     * A scope instance will be returned.
     *
     * @param Scopeable|string $object
     *
     * @return Builder
     *
     * @throws NotScopeableException
     */
    function scope($object)
    {
        $authorizer = app('authorizer');
        $base       = $object;

        if (is_object($object)) {
            $object = clone $object;
        } elseif (is_string($object)) {
            $object = app($object);
        }

        if ($object instanceof Builder) {
            $object = $object->getModel();
        }

        $scope = $authorizer->scope($object);

        if ($base instanceof Builder) {
            $scope->setScope($base);
        }

        return $scope->resolve();
    }
}

if ( ! function_exists('authorizer')) {
    /**
     * Resolve an implementation of the authorization manager from Laravel's service
     * container.
     *
     * @return Authorizer
     */
    function authorizer()
    {
        return app('authorizer');
    }
}
