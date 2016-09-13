<?php
namespace Deefour\Authorizer\Stub;

class ArticleScope
{
    public $scope = true;

    public function resolve()
    {
        return $this->scope;
    }
}
