<?php

namespace spec\Deefour\Authorizer\Stubs;

use Deefour\Authorizer\Stubs\User;
use PhpSpec\ObjectBehavior;

class ArticleScopeSpec extends ObjectBehavior
{
    public function let(User $user)
    {
        $this->beConstructedWith($user, 'bar');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Deefour\Authorizer\Stubs\ArticleScope');
    }

    public function it_should_respond_to_resolve()
    {
        $this->resolve()->shouldBe('bar');
    }
}
