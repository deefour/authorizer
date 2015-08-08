<?php

namespace Deefour\Authorizer;

use Deefour\Producer\Contracts\Producible;

/**
 * Base scope class all application scopes are encouraged to extend. Aide
 * expects a `resolve` method to be present on the scope.
 */
abstract class Scope implements Producible
{
    /**
     * The user.
     *
     * @var mixed
     */
    protected $user;

    /**
     * The base scope.
     *
     * @var mixed
     */
    protected $scope;

    /**
     * Constructor.
     *
     * @param mixed $user
     * @param mixed $scope
     */
    public function __construct($user, $scope)
    {
        $this->user  = $user;
        $this->scope = $scope;
    }

    /**
     * Builds a scoped query based on the current `$user` and `$scope` passed into
     * scope instance.
     *
     * @return mixed
     */
    abstract public function resolve();
}
