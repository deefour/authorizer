<?php

use Deefour\Authorizer\Contracts\AuthorizableContract;

if ( ! function_exists('policy')) {
  /**
   * Retrieve a policy class for the passed object
   *
   * @param  Deefour\Authorizer\Contracts\AuthorizableContract  $object
   * @return Deefour\Authorizer\AbstractPolicy
   */
  function policy(AuthorizableContract $object) {
    return app('authorizer')->policy($object);
  }
}

if ( ! function_exists('scope')) {
  /**
   * Retrieve a scope class for the passed object
   *
   * @param  Deefour\Authorizer\Contracts\AuthorizableContract  $object
   * @return Deefour\Authorizer\AbstractScope
   */
  function scope(AuthorizableContract $object) {
    return app('authorizer')->scope($object);
  }
}

if ( ! function_exists('authorizer')) {
  /**
   * Retrieve the authorizer instance from the IoC container
   *
   * @return Deefour\Authorizer\Authorizer
   */
  function authorizer() {
    return app('authorizer');
  }
}
