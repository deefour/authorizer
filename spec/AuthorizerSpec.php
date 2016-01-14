<?php

namespace spec\Deefour\Authorizer;

use Deefour\Authorizer\Exceptions\AuthorizationNotPerformedException;
use Deefour\Authorizer\Exceptions\NotAuthorizedException;
use Deefour\Authorizer\Exceptions\ScopingNotPerformedException;
use Deefour\Authorizer\Stubs\Article;
use Deefour\Authorizer\Stubs\ArticlePolicy;
use Deefour\Authorizer\Stubs\ArticleScope;
use Deefour\Authorizer\Stubs\Category;
use Deefour\Authorizer\Stubs\User;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AuthorizerSpec extends ObjectBehavior
{
    public function let(User $user)
    {
        $this->beConstructedWith($user);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Deefour\Authorizer\Authorizer');
    }

    public function it_generates_policies()
    {
        $this->policy(new Article())->shouldBeAnInstanceOf(ArticlePolicy::class);
    }

    public function it_generates_scopes()
    {
        $this->scope(new Article())->resolve()->shouldBe('foo');
    }

    public function it_generates_policies_via_get_policy()
    {
        $this->getPolicy(new User(), new Article())->shouldBeAnInstanceOf(ArticlePolicy::class);
    }

    public function it_returns_null_via_get_policy_for_unknown_authorizable()
    {
        $this->getPolicy(new User(), new Category())->shouldReturn(null);
    }

    public function it_generates_policies_via_get_scope()
    {
        $this->getScope(new User(), new Article())->shouldBeAnInstanceOf(ArticleScope::class);
    }

    public function it_returns_null_via_get_scope_for_unknown_authorizable()
    {
        $this->getScope(new User(), new Category())->shouldReturn(null);
    }

    public function it_authorizes_actions()
    {
        $this->shouldThrow(NotAuthorizedException::class)->during('authorize', [new Article(), 'edit']);
        $this->authorize(new Article(), 'create')->shouldBeBoolean();
    }

    public function it_passes_reason_string_through_to_exception_from_policy()
    {
        $this->shouldThrow(new AccessDeniedHttpException('You are not an admin.'))
            ->during('authorize', [new Article(), 'update']);
    }

    public function it_should_pass_additional_context_through_to_policy()
    {
        $this->shouldThrow(NotAuthorizedException::class)->during('authorize', [new Article(), 'destroy', 'bar']);
        $this->authorize(new Article(), 'destroy', 'baz')->shouldReturn(true);
    }

    public function it_should_mark_authorized_flag()
    {
        $this->shouldThrow(AuthorizationNotPerformedException::class)->during('verifyAuthorized');

        $this->authorize(new Article(), 'destroy', 'baz');

        $this->verifyAuthorized()->shouldReturn(null);
    }

    public function it_should_mark_scoped_flag()
    {
        $this->shouldThrow(ScopingNotPerformedException::class)->during('verifyScoped');

        $this->scope(new Article())->resolve()->shouldBe('foo');

        $this->verifyScoped()->shouldReturn(null);
    }

    public function it_should_allow_authorization_to_be_skipped()
    {
        $this->shouldThrow(AuthorizationNotPerformedException::class)->during('verifyAuthorized');

        $this->skipAuthorization();

        $this->verifyAuthorized()->shouldReturn(null);
    }

    public function it_should_allow_scoping_to_be_skipped()
    {
        $this->shouldThrow(ScopingNotPerformedException::class)->during('verifyScoped');

        $this->skipScoping();

        $this->verifyScoped()->shouldReturn(null);
    }
}
