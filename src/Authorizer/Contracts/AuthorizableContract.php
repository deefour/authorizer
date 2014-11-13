<?php namespace Deefour\Authorizer\Contracts;

interface AuthorizableContract {

  public function policyNamespace();

  public function policyClass();

  public function scopeClass();

}
