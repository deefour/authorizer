<?php

namespace Deefour\Authorizer\Stubs;

use Deefour\Authorizer\Policy;

class ArticlePolicy extends Policy
{
    public function create()
    {
        return true;
    }

    public function edit()
    {
        return false;
    }

    public function destroy($foo)
    {
        return $foo === 'baz';
    }

    public function update()
    {
        return 'You are not an admin.';
    }

    public function permittedAttributes()
    {
        return ['title', 'user_id'];
    }
}
