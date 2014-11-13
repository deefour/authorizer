<?php namespace Deefour\Authorizer\Stubs\Foo;

use Deefour\Authorizer\Stubs\Model;

class Article extends Model {

  public function policyNamespace() {
    return 'Deefour\\Authorizer\\Stubs';
  }

  public function policyClass() {
    return static::class . 'Policy';
  }

  public function scopeClass() {
    return static::class . 'Scope';
  }

}
