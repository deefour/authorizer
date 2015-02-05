<?php namespace Deefour\Authorizer;

/**
 * Base scope class all application scopes are encouraged to extend. Aide
 * expects a `resolve` method to be present on the scope
 */
abstract class Scope {

  /**
   * The user
   *
   * @var mixed
   */
  protected $user;

  /**
   * The
   *
   * @var mixed
   */
  protected $scope;


  /**
   * Sets expectations for dependencies on the policy class and stores references
   * to them locally.
   *
   * @param  mixed  $user
   * @param  mixed  $scope
   */
  public function __construct($user, $scope) {
    $this->user   = $user;
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
