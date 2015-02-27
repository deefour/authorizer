<?php

use Deefour\Authorizer\Contracts\Authorizable;



if ( ! function_exists('authorizer')) {
  /**
   * Assign high numeric IDs to a config item to force appending.
   *
   * @param  array  $array
   * @return array
   */
  function policy(Authorizable $object) {
    $authorizer = app('authorizer');

    return $authorizer->policy($object);
  }
}



if ( ! function_exists('authorizer')) {
  /**
   * Assign high numeric IDs to a config item to force appending.
   *
   * @param  array  $array
   * @return array
   */
  function authorizer() {
    return app('authorizer');
  }
}
