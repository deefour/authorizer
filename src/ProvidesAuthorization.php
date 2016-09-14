<?php

namespace Deefour\Authorizer;

use BadMethodCallException;
use Deefour\Authorizer\Exception\AuthorizationNotPerformedException;
use Deefour\Authorizer\Exception\NotAuthorizedException;
use Deefour\Authorizer\Exception\ScopingNotPerformedException;
use Deefour\Transformer\Transformer;

trait ProvidesAuthorization
{
    /**
     *
     * @var bool
     */
    protected $authorizerAuthorized = false;

    /**
     *
     * @var bool
     */
    protected $authorizerScoped = false;

    /**
     *
     * @var array
     */
    protected $authorizerPolicies = [];

    /**
     *
     * @var array
     */
    protected $authorizerPolicyScopes = [];

    /**
     *
     *
     * @api
     * @throws NotAuthorizedException
     * @return mixed
     */
    public function authorize($record, $action = null)
    {
        $action = $action ?: $this->authorizerAction();

        $this->authorizerAuthorized = true;

        $policy  = $this->policy($record);
        $result  = $policy->$action();
        $options = array_merge(compact('query', 'record', 'policy'), [ 'message' => $result ]);

        if ($result !== true) {
            throw new NotAuthorizedException($options);
        }

        return $record;
    }

    /**
     *
     *
     * @api
     * @return mixed
     */
    public function policy($record)
    {
        $hash = is_object($record) ? spl_object_hash($record) : $record;

        if (isset($this->authorizerPolicies[$hash])) {
            return $this->authorizerPolicies[$hash];
        }

        return $this->authorizerPolicies[$hash] = (new Authorizer)->policyOrFail($this->authorizerUser(), $record);
    }

    /**
     *
     *
     * @api
     * @return mixed
     */
    public function scope($scope)
    {
        $this->authorizerPolicyScoped = true;

        return $this->authorizerScope($scope);
    }

    /**
     *
     *
     * @api
     * @return array
     */
    public function permittedAttributes($record, $action = null)
    {
        $action = $action ?: $this->authorizerAction();
        $policy = $this->policy($record);
        $method = 'permittedAttributesFor' . ucfirst($action);
        $whitelist = $policy->permittedAttributes();

        if (method_exists($policy, $method)) {
            $whitelist = $policy->$method();
        }

        $params = new Transformer($this->authorizerAttributes());

        return $params->only($whitelist);
    }

    /**
     *
     *
     * @api
     * @return bool
     */
    public function hasBeenAuthorized()
    {
        return !!$this->authorizerAuthorized;
    }

    /**
     *
     *
     * @api
     * @return bool
     */
    public function hasBeenScoped()
    {
        return !!$this->authorizerScoped;
    }

    /**
     *
     *
     * @api
     * @throws AuthorizationNotPerformedException
     * @return void
     */
    public function verifyAuthorized()
    {
        if ( ! $this->hasBeenAuthorized()) {
            throw new AuthorizationNotPerformedException;
        }
    }

    /**
     *
     *
     * @api
     * @throws ScopingNotPerformedException
     * @return void
     */
    public function verifyScoped()
    {
        if ( ! $this->hasBeenScoped()) {
            throw new ScopingNotPerformedException;
        }
    }

    /**
     *
     *
     * @api
     * @return void
     */
    public function skipAuthorization()
    {
        $this->authorizerAuthorized = true;
    }

    /**
     *
     *
     * @api
     * @return void
     */
    public function skipScoping()
    {
        $this->authorizerScoped = true;
    }

    /**
     *
     * @throws BadMethodCallException
     * @return void
     */
    protected function authorizerAction()
    {
        throw new BadMethodCallException('The authorizerAction method must be defined');
    }

    /**
     *
     * @throws BadMethodCallException
     * @return void
     */
    protected function authorizerUser()
    {
        throw new BadMethodCallException('The authorizerUser method must be defined');
    }

    /**
     *
     * @throws BadMethodCallException
     * @return void
     */
    protected function authorizerAttributes()
    {
        throw new BadMethodCallException('The authorizerAttributes method must be defined');
    }

    /**
     *
     *
     * @throws NotDefinedException
     * @param  mixed $scope
     * @return mixed
     */
    private function authorizerScope($scope)
    {
        $hash = is_object($scope) ? spl_object_hash($scope) : $scope;

        if (isset($this->authorizerScopes[$hash])) {
            return $this->authorizerScopes[$hash];
        }

        return $this->authorizerScopes[$hash] = (new Authorizer)->scopeOrFail($this->authorizerUser(), $scope);
    }
}
