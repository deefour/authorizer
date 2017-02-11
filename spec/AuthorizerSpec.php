<?php

namespace spec\Deefour\Authorizer;

use Deefour\Authorizer\Authorizer;
use Deefour\Authorizer\Exception\NotAuthorizedException;
use Deefour\Authorizer\Exception\NotDefinedException;
use Deefour\Authorizer\Stub\Article;
use Deefour\Authorizer\Stub\ArticlePolicy;
use Deefour\Authorizer\Stub\QueryBuilder;
use Deefour\Authorizer\Stub\Tag;
use Deefour\Authorizer\Stub\User;
use PhpSpec\ObjectBehavior;

class AuthorizerSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(Authorizer::class);
    }

    public function it_can_retrieve_policies_for_valid_records()
    {
        $this->policy(new User, new Article)->shouldReturnAnInstanceOf(ArticlePolicy::class);
    }

    public function it_can_retrieve_scopes_for_valid_records()
    {
        $article = new Article([ 'foo' => 'bar' ]);

        $this->scope(new User, $article)->shouldReturn('bar');
    }

    public function it_will_return_null_for_records_without_policies_or_scopes()
    {
        $this->policy(new User, new Tag)->shouldReturn(null);
        $this->scope(new User, new Tag)->shouldReturn(null);
    }

    public function it_can_throw_an_exception_for_records_without_policies_or_scopes()
    {
        $this->shouldThrow(NotDefinedException::class)->during('policyOrFail', [ new User, new Tag ]);
        $this->shouldThrow(NotDefinedException::class)->during('scopeOrFail', [ new User, new Tag ]);
    }

    public function it_can_find_policies_and_scopes_using_class_name_as_record()
    {
        $this->policy(new User, Article::class)->shouldReturnAnInstanceOf(ArticlePolicy::class);
    }

    public function it_can_authorize_user_against_a_policy()
    {
        $this->shouldThrow(NotAuthorizedException::class)->during('authorize', [new User, Article::class, 'edit']);
    }

    public function it_will_throw_exception_during_authorization_for_record_without_policy()
    {
        $this->shouldThrow(NotDefinedException::class)
            ->during('authorize', [new User, new Tag, 'create']);
    }

    public function it_resolves_scopes_using_help_from_closure()
    {
        $builder = new QueryBuilder([ 'foo' => 'blip' ]);

        $this->scope(new User, $builder, function ($scope) {
            return $scope->source();
        }
        )->shouldReturn('blip');
    }

    public function it_should_filter_attributes()
    {
        $this->permittedAttributes(new User, Article::class, 'store')->shouldBe([ 'bar' ]);
        $this->permittedAttributes(new User, Article::class, 'update')->shouldBe([ 'bar', 'baz' ]);
    }
}
