<?php namespace spec\Deefour\Authorizer\Stubs;

use Deefour\Authorizer\Stubs\User;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ArticleScopeSpec extends ObjectBehavior {

  function let(User $user) {
    $this->beConstructedWith($user, 'bar');
  }

  function it_is_initializable() {
    $this->shouldHaveType('Deefour\Authorizer\Stubs\ArticleScope');
  }

  function it_should_respond_to_resolve() {
    $this->resolve()->shouldBe('bar');
  }

}
