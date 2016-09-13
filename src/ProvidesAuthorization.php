<?php

namespace Deefour\Authorizer;

use BadMethodCallException;
use Deefour\Authorizer\Exception\AuthorizationNotPerformedException;
use Deefour\Authorizer\Exception\NotAuthorizedException;
use Deefour\Authorizer\Exception\ScopingNotPerformedException;
use Deefour\Transformer\Transformer;

trait ProvidesAuthorization
{
    protected $authorizerAuthorized = false;

    protected $authorizerScoped = false;

    protected $authorizerPolicies = [];

    protected $authorizerPolicyScopes = [];

    public function hasBeenAuthorized()
    {
        return !!$this->authorizerAuthorized;
    }

    public function hasBeenScoped()
    {
        return !!$this->authorizerScoped;
    }

    public function verifyAuthorized()
    {
        if ( ! $this->hasBeenAuthorized()) {
            throw new AuthorizationNotPerformedException;
        }
    }

    public function verifyScoped()
    {
        if ( ! $this->hasBeenScoped()) {
            throw new ScopingNotPerformedException;
        }
    }

    public function authorize($record, $query = null)
    {
        $query = $query ?: $this->authorizerAction();

        $this->authorizerAuthorized = true;

        $policy  = $this->policy($record);
        $result  = $policy->$query();
        $options = array_merge(compact('query', 'record', 'policy'), [ 'message' => $result ]);

        if ($result !== true) {
            throw new NotAuthorizedException($options);
        }

        return $record;
    }

    public function policy($record)
    {
        $hash = is_object($record) ? spl_object_hash($record) : $record;

        if (isset($this->authorizerPolicies[$hash])) {
            return $this->authorizerPolicies[$hash];
        }

        return $this->authorizerPolicies[$hash] = (new Authorizer)->policyOrFail($this->authorizerUser(), $record);
    }

    public function scope($scope)
    {
        $this->authorizerPolicyScoped = true;

        return $this->authorizerScope($scope);
    }

    public function skipAuthorization()
    {
        $this->authorizerAuthorized = true;
    }

    public function skipScoping()
    {
        $this->authorizerScoped = true;
    }

    public function permittedAttributes($record, $action = null)
    {
        $action = $action ?: $this->authorizerAction();
        $policy = $this->policy($record);
        $method = 'permittedAttributesFor' . ucfirst($action);
        $whitelist = $policy->permittedAttributes();

        if (method_exists($policy, $method)) {
            $whitelist = $policy->$method();
        }

        $params = new Transformer($this->authorizerParams());

        return $params->only($whitelist);
    }

    protected function authorizerAction()
    {
        throw new BadMethodCallException('The authorizerAction method must be defined');
    }

    protected function authorizerUser()
    {
        throw new BadMethodCallException('The authorizerUser method must be defined');
    }

    protected function authorizerParams()
    {
        throw new BadMethodCallException('The authorizerParams method must be defined');
    }

    private function authorizerScope($scope)
    {
        $hash = is_object($scope) ? spl_object_hash($scope) : $scope;

        if (isset($this->authorizerScopes[$hash])) {
            return $this->authorizerScopes[$hash];
        }

        return $this->authorizerScopes[$hash] = (new Authorizer)->scopeOrFail($this->authorizerUser(), $scope);
    }
}
