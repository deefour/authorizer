<?php

namespace spec\Deefour\Authorizer\Stubs;

use Deefour\Authorizer\Stubs\Article;
use Deefour\Authorizer\Stubs\User;
use PhpSpec\ObjectBehavior;

class ArticlePolicySpec extends ObjectBehavior
{
    public function let(User $user, Article $article)
    {
        $this->beConstructedWith($user, $article);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Deefour\Authorizer\Stubs\ArticlePolicy');
    }

    public function it_should_respond_to_actions()
    {
        $this->edit()->shouldBeBoolean();
    }

    public function it_should_call_action_via_can()
    {
        $this->can('edit')->shouldBeBoolean();
    }

    public function it_should_throw_exception_for_bad_exception_via_can()
    {
        $this->shouldThrow('\\BadMethodCallException')->during('can', ['bad']);
    }

    public function it_should_respond_to_permitted_attributes()
    {
        $this->permittedAttributes()->shouldBeArray();
    }
}
