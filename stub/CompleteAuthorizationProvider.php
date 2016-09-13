<?php

namespace Deefour\Authorizer\Stub;

use Deefour\Authorizer\ProvidesAuthorization;

class CompleteAuthorizationProvider
{
    use ProvidesAuthorization;

    protected function authorizerAction()
    {
        return 'create';
    }

    protected function authorizerUser()
    {
        return new User;
    }

    protected function authorizerAttributes()
    {
        return [ 'foo' => 1, 'bar' => 2, 'baz' => 3 ];
    }
}
