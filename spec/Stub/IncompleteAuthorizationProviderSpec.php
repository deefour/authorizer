<?php

namespace spec\Deefour\Authorizer\Stub;

use BadMethodCallException;
use Deefour\Authorizer\Stub\Article;
use Deefour\Authorizer\Stub\IncompleteAuthorizationProvider;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class IncompleteAuthorizationProviderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(IncompleteAuthorizationProvider::class);
    }

    function it_fails_on_policy_lookup()
    {
        $this->shouldThrow(BadMethodCallException::class)->during('policy', [ new Article ]);
    }

    function it_fails_on_permitted_attributes_lookup()
    {
        $this->shouldThrow(BadMethodCallException::class)->during('permittedAttributes', [ new Article ]);
    }

    function it_fails_on_scoping()
    {
        $this->shouldThrow(BadMethodCallException::class)->during('scope', [ new Article ]);
    }
}
