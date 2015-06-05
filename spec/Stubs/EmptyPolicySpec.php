<?php

namespace spec\Deefour\Authorizer\Stubs;

use Deefour\Authorizer\Stubs\Article;
use Deefour\Authorizer\Stubs\User;
use PhpSpec\ObjectBehavior;

class EmptyPolicySpec extends ObjectBehavior
{
    public function let(User $user, Article $article)
    {
        $this->beConstructedWith($user, $article);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Deefour\Authorizer\Stubs\EmptyPolicy');
    }
}
