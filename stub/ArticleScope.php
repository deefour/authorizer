<?php

namespace Deefour\Authorizer\Stubs;

use Deefour\Authorizer\Scope;

class ArticleScope extends Scope
{
    public function resolve()
    {
        return $this->scope;
    }
}
