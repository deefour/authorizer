<?php namespace Deefour\Authorizer\Contracts;

interface Authorizable {

  /**
   * The namespace to use for the policy class lookups.
   *
   * @return string
   */
  public function policyNamespace();

  /**
   * The namespace to use for the scope class lookups.
   *
   * @return string
   */
  public function scopeNamespace();

  /**
   * Generates the name of the policy class, usually based off of the name of the
   * class implementing this contract.
   *
   * @return string
   */
  public function policyClass();

  /**
   * Generates the name of the scope class, usually based off of the name of the
   * class implementing this contract.
   *
   * @return string
   */
  public function scopeClass();

}
