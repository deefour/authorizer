<?php

namespace Deefour\Authorizer;

use Deefour\Producer\Contracts\Producible;
use Deefour\Authorizer\Contracts\Authorizee;

/**
 * Base scope class all application scopes are encouraged to extend. Aide
 * expects a `resolve` method to be present on the scope.
 */
abstract class Scope implements Producible
{
    /**
     * The user.
     *
     * @var Authorizee
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
    public function __construct(Authorizee $user, $scope)
    {
        $this->user  = $user;

        $this->setScope($scope);
    }

    /**
     * Provides ability for scope to be overriden after the instance has been
     * instantiated
     *
     * @param mixed $scope
     * @return void
     */
    public function setScope($scope)
    {
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
