<?php namespace spec\Deefour\Authorizer;

use Deefour\Authorizer\Exceptions\NotAuthorizedException;
use Deefour\Authorizer\Stubs\Article;
use Deefour\Authorizer\Stubs\ArticlePolicy;
use Deefour\Authorizer\Stubs\User;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AuthorizerSpec extends ObjectBehavior {

  function let(User $user) {
    $this->beConstructedWith($user);
  }

  function it_is_initializable() {
    $this->shouldHaveType('Deefour\Authorizer\Authorizer');
  }

  function it_generates_policies() {
    $this->policy(new Article)->shouldBeAnInstanceOf(ArticlePolicy::class);
  }

  function it_generates_scopes() {
    $this->scope(new Article)->shouldBe('foo');
  }

  public function it_authorizes_actions() {
    $this->shouldThrow(NotAuthorizedException::class)->during('authorize', [ new Article, 'edit' ]);
    $this->authorize(new Article, 'create')->shouldBeBoolean();
  }

  function it_should_pass_additional_context_through_to_policy() {
    $this->shouldThrow(NotAuthorizedException::class)->during('authorize', [ new Article, 'destroy', 'bar' ]);
    $this->authorize(new Article, 'destroy', 'baz')->shouldReturn(true);
  }

}
