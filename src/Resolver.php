<?php

namespace Deefour\Authorizer;

use Deefour\Authorizer\Exception\NotDefinedException;
use ReflectionClass;

class Resolver
{
    const POLICY = 'Policy';

    const SCOPE  = 'Scope';

    public $object;

    public function __construct($object)
    {
        $this->object = $object;
    }

    public function scope()
    {
        $klass = $this->find(static::SCOPE);

        return class_exists($klass) ? $klass : null;
    }

    public function policy()
    {
        $klass = $this->find();

        return class_exists($klass) ? $klass : null;
    }

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

    protected function find($suffix = null)
    {
        $suffix = $suffix ?: static::POLICY;

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
     *
     * @link  https://github.com/symfony/symfony/blob/2.8/src/Symfony/Component/DependencyInjection/Container.php#L575
     * @param  mixed $subject
     * @return string
     */
    protected function findClassName($subject)
    {
        if (is_string($subject) && ! class_exists($subject)) {
            return null;
        };

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
