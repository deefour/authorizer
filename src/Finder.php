<?php

namespace Deefour\Authorizer;

use Deefour\Authorizer\Contracts\Authorizable as AuthorizableContract;
use Deefour\Authorizer\Exceptions\NotAuthorizableException;
use Deefour\Authorizer\Exceptions\NotDefinedException;
use ReflectionClass;

/**
 * Derives the full class name for a policy or scope based on a passed object.
 *
 * There is support for silent or noisy failure if a class could not be
 * provided.
 *
 * This class does not actually instantiate the derived class - it simply
 * returns the class' name
 */
class Finder
{
    /**
     * A flag telling the finder to derive a policy class name.
     *
     * @const
     *
     * @var string
     */
    const POLICY = 'policy';

    /**
     * A flag telling the finder to derive a scope class name.
     *
     * @const
     *
     * @var string
     */
    const SCOPE = 'scope';

    /**
     * The object to derive the scope or policy class name from.
     *
     * @var mixed
     */
    protected $object;

    public function __construct($object)
    {
        $this->object = $object;
    }

    /**
     * Derives a scope class name for the object the finder was passed when
     * instantiated. There is no check made here to see if the class actually
     * exists.
     *
     * @return string
     */
    public function scope()
    {
        return $this->find(self::SCOPE);
    }

    /**
     * Derives a policy class name for the object the finder was passed when
     * instantiated. There is no check made here to see if the class actually
     * exists.
     *
     * @return string
     */
    public function policy()
    {
        return $this->find(self::POLICY);
    }

    /**
     * Derives a scope class name for the object the finder was passed when
     * instantiated. There is no check made here to see if the class actually
     * exists.
     *
     * Fails loudly if the derived scope class does not exist.
     *
     * @throws NotDefinedException
     *
     * @return string
     */
    public function scopeOrFail()
    {
        return $this->getOrFail(self::SCOPE);
    }

    /**
     * Derives a policy class name for the object the finder was passed when
     * instantiated. There is no check made here to see if the class actually
     * exists.
     *
     * Fails loudly if the derived policy class does not exist.
     *
     * @throws NotDefinedException
     *
     * @return string
     */
    public function policyOrFail()
    {
        return $this->getOrFail(self::POLICY);
    }

    /**
     * Derives the class name for the object the finder was passed when
     * instantiated.
     *
     * @param  $type  string
     *
     * @return string
     *
     * @throws NotAuthorizableException
     */
    protected function find($type)
    {
        if (!($this->object instanceof AuthorizableContract)) {
            throw new NotAuthorizableException(sprintf(
                'The [%s] object does not implement [%s]; authorization cannot be performed',
                get_class($this->object),
                AuthorizableContract::class
            ));
        }

        $namespace = $this->object->{"${type}Namespace"}();
        $klass     = $this->object->{"${type}Class"}();

        if (class_exists($klass)) {
            return $klass;
        }

        $klass = implode('\\', [$namespace, $klass]);

        if (class_exists($klass)) {
            return $klass;
        }

        $shortName = (new ReflectionClass($this->object))->getShortName();

        return implode('\\', [$namespace, $shortName.ucfirst($type)]);
    }

    /**
     * Find a policy or scope. Throw an exception otherwise.
     *
     * @throws NotDefinedException
     * @param  string  $which
     * @return Policy|Scope
     */
    protected function getOrFail($which)
    {
        $what = $this->$which();

        if (class_exists($what)) {
            return $what;
        }

        throw new NotDefinedException(sprintf(
            'Unable to find %s class for [%s]',
            $which,
            get_class($this->object)
        ));
    }
}
