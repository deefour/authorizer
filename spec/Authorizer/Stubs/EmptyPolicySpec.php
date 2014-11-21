<?php namespace spec\Deefour\Authorizer\Stubs;

use BadMethodCallException;
use Deefour\Authorizer\Stubs\User;
use Deefour\Authorizer\Stubs\Article;
use Deefour\Authorizer\Stubs\EmptyPolicy;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class EmptyPolicySpec extends ObjectBehavior {

  function let(User $user, Article $article) {
    $this->beConstructedWith($user, $article);
  }

  function it_is_initializable() {
    $this->shouldHaveType(EmptyPolicy::class);
  }

  function it_should_throw_exception_when_permitted_attributes_is_undefined() {
    $this->shouldThrow(BadMethodCallException::class)->during('permittedAttributes');
  }

}
