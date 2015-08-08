<?php

namespace Deefour\Authorizer\Stubs;

class FeaturedArticleScope extends ArticleScope
{
  public function resolve()
  {
      return $this->scope;
  }
}
