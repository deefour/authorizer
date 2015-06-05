<?php

namespace Deefour\Authorizer\Contracts;

interface Scopeable
{
    /**
     * The namespace to use for the scope class lookups.
     *
     * @return string
     */
    public function scopeNamespace();

    /**
     * Generates the name of the scope class, usually based off of the name of the
     * class implementing this contract.
     *
     * @return string
     */
    public function scopeClass();

    /**
     * Common entry-point for the base query object the scope resolution will be
     * based off of.
     *
     * @return mixed
     */
    public function baseScope();
}
