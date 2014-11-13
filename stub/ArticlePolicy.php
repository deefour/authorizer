<?php namespace Deefour\Authorizer\Stubs;

use Deefour\Authorizer\AbstractPolicy;

class ArticlePolicy extends AbstractPolicy {

  public function create() {
    return true;
  }

  public function edit() {
    return false;
  }

  public function permittedAttributes() {
    return [ 'title', 'user_id' ];
  }

}