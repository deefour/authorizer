<?php

namespace Deefour\Authorizer\Stub;

class ArticleScope
{
    public $user;

    public $scope;

    public function __construct($user, $scope)
    {
        $this->user  = $user;
        $this->scope = $scope;
    }

    public function resolve()
    {
        return $this->scope->foo;
    }
}
