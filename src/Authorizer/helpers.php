<?php

use Deefour\Authorizer\Contracts\Authorizable;
use Deefour\Authorizer\Contracts\ResolvesAuthorizable;
use Deefour\Authorizer\Contracts\Scopeable;
use Deefour\Authorizer\Exceptions\NotScopeableException;

if ( ! function_exists('policy')) {
  /**
   * Assign high numeric IDs to a config item to force appending.
   *
   * @param  Authorizable|string $object
   *
   * @return array
   */
  function policy($object) {
    $authorizer = app('authorizer');

    if (is_string($object)) {
      $object = app($object);
    }

    if ($object instanceof ResolvesAuthorizable) {
      $object = $object->resolveAuthorizable();
    }

    return $authorizer->policy($object);
  }
}

if ( ! function_exists('scope')) {
  /**
   * Assign high numeric IDs to a config item to force appending.
   *
   * @param  Scopeable|string $object
   *
   * @return array
   */
  function scope($object) {
    $authorizer = app('authorizer');

    if (is_string($object)) {
      $object = app($object);
    }

    if ( ! ($object instanceof Scopeable)) {
      throw new NotScopeableException(sprintf(
        'A $scope must be passed to the scope() helper when $object doesn\'t ' .
        'implement [%s]. The $object passed was [%s].',
        Scopeable::class,
        get_class($object)
      ));
    }

    return $authorizer->scope($object);
  }
}

if ( ! function_exists('authorizer')) {
  /**
   * Assign high numeric IDs to a config item to force appending.
   *
   * @return \Deefour\Authorizer\Authorizer
   */
  function authorizer() {
    return app('authorizer');
  }
}
