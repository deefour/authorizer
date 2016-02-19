<?php

namespace Deefour\Authorizer;

use Deefour\Authorizer\Contracts\Authorizee;

/**
 * Provides easy access to much of Authorizer's functionality.
 *
 * Policy/Scope lookups and authorization can be performed against an
 * instance using a select subset of methods found on the
 * `Deefour\Authorizer\ProvidesAuthorization` class.
 *
 *   $authorizer->authorize(new Article, 'create'); //=> boolean
 *   $authorizer->policy(new Article); //=> ArticlePolicy
 *   $authorizer->scope(new Article);  //=> ArticleScope
 */
class Authorizer
{
    use ProvidesAuthorization;

    /**
     * The current user.
     *
     * @var Authorizee
     */
    protected $user;

    /**
     * Configure the policy class with the current user and context.
     *
     * @param Authorizee $user
     */
    public function __construct(Authorizee $user = null)
    {
        $this->user = $user;
    }

    /**
     * @inheritdoc
     */
    protected function user()
    {
        return $this->user;
    }
}
