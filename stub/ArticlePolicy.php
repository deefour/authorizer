<?php

namespace Deefour\Authorizer\Stub;

class ArticlePolicy
{
    public function create()
    {
        return true;
    }

    public function edit()
    {
        return false;
    }

    public function update()
    {
        return 'You are not an admin.';
    }

    public function permittedAttributes()
    {
        return ['bar'];
    }

    public function permittedAttributesForUpdate()
    {
        return ['bar', 'baz'];
    }
}
