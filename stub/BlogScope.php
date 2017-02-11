<?php

namespace Deefour\Authorizer\Stub;

use StdClass;

class BlogScope
{
    public $scope;

    public function __construct()
    {
        $this->scope = new StdClass;
    }

    public function resolve()
    {
        return $this->scope;
    }
}
