<?php

namespace Deefour\Authorizer\Stub;

use Deefour\Transformer\Transformer;

class QueryBuilder extends Transformer
{
    public function source()
    {
        return new Article;
    }
}
