<?php

namespace spec\Deefour\Authorizer;

use Deefour\Authorizer\Exceptions\NotDefinedException;
use Deefour\Authorizer\Stubs\Article;
use Deefour\Authorizer\Stubs\ArticlePolicy;
use Deefour\Authorizer\Stubs\Category;
use Deefour\Authorizer\Stubs\Foo\Article as FooArticle;
use PhpSpec\ObjectBehavior;
use ReflectionClass;

class FinderSpec extends ObjectBehavior
{
    public function it_is_initializable(Article $article)
    {
        $this->beConstructedWith($article);

        $this->shouldHaveType('Deefour\Authorizer\Finder');
    }

    public function it_will_always_return_class_name_by_default()
    {
        $expectedBase = '\\'.(new ReflectionClass(Category::class))->getShortName();

        $this->beConstructedWith(new Category());

        $this->policy()->shouldBe($expectedBase.'Policy');
        $this->scope()->shouldBe($expectedBase.'Scope');
    }

    public function it_will_throw_exceptions_when_asked_to_fail()
    {
        $this->beConstructedWith(new Category()); // Category has no matching policy or scope

    $this->shouldThrow(NotDefinedException::class)->during('policyOrFail');
        $this->shouldThrow(NotDefinedException::class)->during('scopeOrFail');
    }

    public function it_will_resolve_using_authorizable_overrides()
    {
        $this->beConstructedWith(new FooArticle());

        $this->policy()->shouldBe(ArticlePolicy::class);
    }
}
