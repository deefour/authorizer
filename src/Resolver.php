<?php

namespace Deefour\Authorizer;

use Deefour\Authorizer\Exception\NotDefinedException;
use ReflectionClass;

/**
 * Attempts to find the name of policy and scope objects for a provided $object.
 */
class Resolver
{
    /**
     * The default suffix for policy class resolution.
     *
     * @var string
     */
    const POLICY = 'Policy';

    /**
     * The default suffix for scope class resolution.
     *
     * @var string
     */
    const SCOPE  = 'Scope';

    /**
     * The object to resolve policy or scope classes for.
     *
     * @var mixed
     */
    public $object;

    /**
     * Constructor.
     *
     * @param mixed $object
     */
    public function __construct($object)
    {
        $this->object = $object;
    }

    /**
     * Resolve the FQN of the policy class related to the object set on this class
     * instance.
     *
     * @return string|null
     */
    public function policy()
    {
        $klass = $this->find();

        return class_exists($klass) ? $klass : null;
    }

    /**
     * Resolve the FQN of the scope class related to the object set on this class
     * instance.
     *
     * @return string|null
     */
    public function scope()
    {
        $klass = $this->find(static::SCOPE);

        return class_exists($klass) ? $klass : null;
    }

    /**
     * Resolve the FQN of the policy class related to the object set on this class
     * instance. Throw an exception if the class could not be resolved.
     *
     * @throws \Deefour\Authorizer\Exception\NotDefinedException
     * @return string
     */
    public function policyOrFail()
    {
        if (is_null($this->object)) {
            throw new NotDefinedException('Unable to find policy of null');
        }

        if ($policy = $this->policy()) {
            return $policy;
        }

        throw new NotDefinedException('Unable to find policy for ' . get_class($this->object));
    }

    /**
     * Resolve the FQN of the scope class related to the object set on this class
     * instance. Throw an exception if the class could not be resolved.
     *
     * @throws \Deefour\Authorizer\Exception\NotDefinedException
     * @return string
     */
    public function scopeOrFail()
    {
        if (is_null($this->object)) {
            throw new NotDefinedException('Unable to find policy scope of null');
        }

        if ($scope = $this->scope()) {
            return $scope;
        }

        throw new NotDefinedException('unable to find scope for ' . get_class($this->object));
    }

    /**
     * Resolve the class name using the $suffix as the type of class to resolve
     * for the $object on this class instance.
     *
     * If the $object is a class instance, reflection is used to statically call
     * a policyClass() or scopeClass() method on the $object to get the class name
     * for the policy or scope respectively.
     *
     * If the $object is a string, further reflection is used to determine the FQN
     * of a related class instance to treat as the source of the policy or scope
     * name.
     *
     * @param  string     $suffix
     * @return mixed|null
     */
    public function find($suffix = self::POLICY)
    {
        if (is_null($this->object)) {
            return null;
        }

        if (is_object($this->object)) {
            $reflection   = new ReflectionClass($this->object);
            $lookupMethod = strtolower($suffix) . 'Class';

            if ($reflection->hasMethod($lookupMethod)) {
                return call_user_func(join('::', [ $reflection->name, $lookupMethod ]));
            }
        }

        if ($base = $this->findClassName($this->object)) {
            return $base . $suffix;
        }

        return null;
    }

    /**
     * Attempt to use reflecton to determine the FQN of a class related to $subject
     * that should be treated as the soure of the policy or scope name. The
     * reflection checks for a static modelName() method on the $subject. 'Policy'
     * or 'Scope' will be appended to the returned FQN.
     *
     * @param  mixed  $subject
     * @return string
     */
    protected function findClassName($subject)
    {
        if (is_string($subject) && ! class_exists($subject)) {
            return null;
        }

        $reflection = new ReflectionClass($subject);

        if ($reflection->hasMethod('modelName')) {
            return call_user_func($reflection->name . '::modelName');
        }

        if (is_string($subject)) {
            return $subject;
        }

        return get_class($subject);
    }
}
