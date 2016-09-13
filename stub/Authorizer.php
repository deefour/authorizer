<?php

namespace Deefour\Authorizer\Stub;

use Deefour\Authorizer\ProvidesAuthorization;

class Authorizer
{
    use ProvidesAuthorization;

    protected function authorizerUser()
    {
        return $this->user;
    }

    protected function authorizerAttributes()
    {
        return $this->params;
    }

    protected function authorizerAction()
    {
        return $this->action;
    }
}
