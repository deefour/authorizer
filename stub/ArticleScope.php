<?php namespace Deefour\Authorizer\Stubs;

use Deefour\Authorizer\AbstractScope;

class ArticleScope extends AbstractScope {

  public function resolve() {
    return [];
  }

}