<?php namespace spec\Deefour\Authorizer\Stubs;

use Deefour\Authorizer\Stubs\User;
use Deefour\Authorizer\Stubs\Article;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ArticlePolicySpec extends ObjectBehavior {

  function let(User $user, Article $article) {
    $this->beConstructedWith($user, $article);
  }

  function it_is_initializable() {
    $this->shouldHaveType('Deefour\Authorizer\Stubs\ArticlePolicy');
  }

  function it_should_respond_to_actions() {
    $this->edit()->shouldBeBoolean();
  }

  function it_should_call_action_via_can() {
    $this->can('edit')->shouldBeBoolean();
  }

  function it_should_throw_exception_for_bad_exception_via_can() {
    $this->shouldThrow('\\BadMethodCallException')->during('can', [ 'bad' ]);
  }

  function it_should_respond_to_permitted_attributes() {
    $this->permittedAttributes()->shouldBeArray();
  }

}
