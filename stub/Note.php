<?php

namespace Deefour\Authorizer\Stub;

class Note
{
    static public function policyClass()
    {
        return ArticlePolicy::class;
    }

    static public function scopeClass()
    {
        return BlogScope::class;
    }
}
