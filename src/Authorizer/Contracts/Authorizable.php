<?php namespace Deefour\Authorizer\Contracts;

interface Authorizable {

  /**
   * The namespace to use for the policy class lookups.
   *
   * @return string
   */
  public function policyNamespace();

  /**
   * Generates the name of the policy class, usually based off of the name of the
   * class implementing this contract.
   *
   * @return string
   */
  public function policyClass();

}
