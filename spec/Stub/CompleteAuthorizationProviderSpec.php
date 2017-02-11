<?php

namespace spec\Deefour\Authorizer\Stub;

use Deefour\Authorizer\Exception\AuthorizationNotPerformedException;
use Deefour\Authorizer\Exception\NotAuthorizedException;
use Deefour\Authorizer\Exception\NotDefinedException;
use Deefour\Authorizer\Exception\ScopingNotPerformedException;
use Deefour\Authorizer\Stub\Article;
use Deefour\Authorizer\Stub\ArticlePolicy;
use Deefour\Authorizer\Stub\Blog;
use Deefour\Authorizer\Stub\CompleteAuthorizationProvider;
use Deefour\Authorizer\Stub\QueryBuilder;
use Deefour\Authorizer\Stub\Tag;
use PhpSpec\ObjectBehavior;

class CompleteAuthorizationProviderSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(CompleteAuthorizationProvider::class);
    }

    public function it_should_not_be_marked_as_authorized_or_scoped_by_default()
    {
        $this->hasBeenAuthorized()->shouldReturn(false);
        $this->hasBeenScoped()->shouldReturn(false);
    }

    public function it_can_allow_scoping_or_authorization_to_be_skipped()
    {
        $this->shouldThrow(AuthorizationNotPerformedException::class)->during('verifyAuthorized');
        $this->hasBeenAuthorized()->shouldReturn(false);

        $this->skipAuthorization();

        $this->verifyAuthorized();

        $this->hasBeenAuthorized()->shouldReturn(true);
        $this->shouldThrow(ScopingNotPerformedException::class)->during('verifyScoped');
        $this->hasBeenScoped()->shouldReturn(false);

        $this->skipScoping();

        $this->verifyScoped();

        $this->hasBeenScoped()->shouldReturn(true);
    }

    public function it_should_resolve_policies()
    {
        $this->policy(new Article)->shouldReturnAnInstanceOf(ArticlePolicy::class);
        $this->shouldThrow(NotDefinedException::class)->during('policy', [ new Tag ]);
    }

    public function it_should_resolve_scopes()
    {
        $article = new Article([ 'foo' => 'baz' ]);

        $this->scope($article)->shouldReturn('baz');
        $this->shouldThrow(NotDefinedException::class)->during('scope', [ new Tag ]);
    }

    public function it_should_cache_retrieved_policies()
    {
        $policy = $this->policy($article = new Article);

        $this->policy(new Article)->shouldNotReturn($policy);
        $this->policy($article)->shouldReturn($policy);
    }

    public function it_should_cache_retrieved_scopes()
    {
        $scope = $this->scope($blog = new Blog);

        $this->scope($blog)->shouldReturn($scope);
        $this->scope(new Blog)->shouldNotReturn($scope);
    }

    public function it_should_authorize_actions()
    {
        $this->authorize($article = new Article, 'create')->shouldReturn($article);

        $this->hasBeenAuthorized()->shouldReturn(true);

        $this->authorize($article = new Article)->shouldReturn($article);

        $this->shouldThrow(NotAuthorizedException::class)->during('authorize', [ new Article, 'edit' ]);
    }

    public function it_should_filter_attributes()
    {
        $this->permittedAttributes(Article::class, 'store')->shouldHaveKey('bar');
        $this->permittedAttributes(Article::class, 'store')->shouldNotHaveKey('baz');
        $this->permittedAttributes(Article::class, 'store')->shouldNotHaveKey('foo');

        $this->permittedAttributes(Article::class, 'update')->shouldHaveKey('bar');
        $this->permittedAttributes(Article::class, 'update')->shouldHaveKey('baz');
        $this->permittedAttributes(Article::class, 'update')->shouldNotHaveKey('foo');
    }

    public function it_resolves_scopes_using_help_from_closure()
    {
        $builder = new QueryBuilder([ 'foo' => 'bar' ]);

        $this->scope($builder, function ($scope) {
            return $scope->source();
        })->shouldReturn('bar');
    }
}
