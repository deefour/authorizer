<?php namespace Deefour\Authorizer\Contracts;

interface Scopeable {

  /**
   * Common entry-point for the base query object the scope resolution will be
   * based off of.
   *
   * @return mixed
   */
  public function baseScope();

}
