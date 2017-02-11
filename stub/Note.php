<?php

namespace Deefour\Authorizer\Stub;

class Note
{
    public static function policyClass()
    {
        return ArticlePolicy::class;
    }

    public static function scopeClass()
    {
        return BlogScope::class;
    }
}
