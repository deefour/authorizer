<?php

namespace spec\Deefour\Authorizer;

use Deefour\Authorizer\Exception\NotDefinedException;
use Deefour\Authorizer\Resolver;
use Deefour\Authorizer\Stub\Article;
use Deefour\Authorizer\Stub\ArticlePolicy;
use Deefour\Authorizer\Stub\ArticleScope;
use Deefour\Authorizer\Stub\BlogScope;
use Deefour\Authorizer\Stub\Note;
use Deefour\Authorizer\Stub\Quote;
use Deefour\Authorizer\Stub\Tag;
use PhpSpec\ObjectBehavior;

class ResolverSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->beConstructedWith(new Tag);
        $this->shouldHaveType(Resolver::class);
    }

    public function it_returns_null_when_finding_for_null()
    {
        $this->beConstructedWith(null);
        $this->policy()->shouldReturn(null);
        $this->scope()->shouldReturn(null);
    }

    public function it_returns_null_for_failed_find()
    {
        $this->beConstructedWith(new Tag);
        $this->policy()->shouldReturn(null);
        $this->scope()->shouldReturn(null);
    }

    public function it_throws_exception_for_null_during_strict_check()
    {
        $this->beConstructedWith(null);
        $this->shouldThrow(NotDefinedException::class)->during('policyOrFail');
        $this->shouldThrow(NotDefinedException::class)->during('scopeOrFail');
    }

    public function it_throws_exception_for_failed_find_during_strict_check()
    {
        $this->beConstructedWith(new Tag);
        $this->shouldThrow(NotDefinedException::class)->during('policyOrFail');
        $this->shouldThrow(NotDefinedException::class)->during('scopeOrFail');
    }

    public function it_finds_by_appending_suffix()
    {
        $this->beConstructedWith(new Article);
        $this->policy()->shouldReturn(ArticlePolicy::class);
        $this->scope()->shouldReturn(ArticleScope::class);
        $this->policyOrFail()->shouldReturn(ArticlePolicy::class);
        $this->scopeOrFail()->shouldReturn(ArticleScope::class);
    }

    public function it_finds_by_resolving_model_names()
    {
        $this->beConstructedWith(new Quote);
        $this->policy()->shouldReturn(ArticlePolicy::class);
        $this->scope()->shouldReturn(ArticleScope::class);
    }

    public function it_finds_from_class_names()
    {
        $this->beConstructedWith(Article::class);
        $this->policy()->shouldReturn(ArticlePolicy::class);
        $this->scope()->shouldReturn(ArticleScope::class);
    }

    public function it_finds_from_invalid_class_names()
    {
        $this->beConstructedWith('InvalidClassNameHere');
        $this->policy()->shouldReturn(null);
        $this->scope()->shouldReturn(null);
    }

    public function it_finds_from_policy_and_scope_model_methods()
    {
        $this->beConstructedWith(new Note);
        $this->policy()->shouldReturn(ArticlePolicy::class);
        $this->scope()->shouldReturn(BlogScope::class);
    }
}
