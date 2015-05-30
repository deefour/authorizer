<?php namespace Deefour\Authorizer\Contracts;

interface ResolvesAuthorizable {

  /**
   * Resolves an authorizable object linked to this implementing resource.
   *
   * @return Authorizable
   */
  public function resolveAuthorizable();

}
