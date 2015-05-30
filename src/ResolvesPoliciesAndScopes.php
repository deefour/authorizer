<?php namespace Deefour\Authorizer;

trait ResolvesPoliciesAndScopes {

  /**
   * {@inheritdoc}
   */
  public function policyNamespace() {
    return '';
  }

  /**
   * {@inheritdoc}
   */
  public function scopeNamespace() {
    return '';
  }

  /**
   * {@inheritdoc}
   */
  public function policyClass() {
    return static::class . 'Policy';
  }

  /**
   * {@inheritdoc}
   */
  public function scopeClass() {
    return static::class . 'Scope';
  }

}
