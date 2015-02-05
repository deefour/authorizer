<?php namespace Deefour\Authorizer\Contracts;

interface Authorizable {

  public function policyNamespace();

  public function policyClass();

  public function scopeClass();

}
