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

  function it_denies_access_to_non_whitelisted_api_methods() {
    $this->shouldThrow('\BadMethodCallException')->during('currentUser');
  }

  function it_generates_policies() {
    $this->policy(new Article)->shouldBeAnInstanceOf(ArticlePolicy::class);
  }

  function it_generates_scopes() {
    $this->scope(new Article)->shouldBeArray();
  }

  public function it_authorizes_actions() {
    $this->shouldThrow(NotAuthorizedException::class)->during('authorize', [ new Article, 'edit' ]);
    $this->authorize(new Article, 'create')->shouldBeBoolean();
  }

  function it_allows_static_api_access() {
    $this::policy(new Article)->shouldBeAnInstanceOf(ArticlePolicy::class);
    $this::scope(new Article)->shouldBeArray();
  }

}
