<?php namespace spec\Deefour\Authorizer\Stubs;

use Deefour\Authorizer\Stubs\User;
use Deefour\Authorizer\Stubs\Article;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class EmptyPolicySpec extends ObjectBehavior {

  function let(User $user, Article $article) {
    $this->beConstructedWith($user, $article);
  }

  function it_is_initializable() {
    $this->shouldHaveType('Deefour\Authorizer\Stubs\EmptyPolicy');
  }

}
