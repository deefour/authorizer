<?php namespace Deefour\Authorizer;

use Deefour\Authorizer\Traits\ProvidesAuthorization;

/**
 * Provides easy access to much of Authorizer's functionality.
 *
 * Policy/Scope lookups and authorization can be performed against an
 * instance using a select subset of methods found on the
 * `Deefour\Authorizer\Traits\ProvidesAuthorization` class.
 *
 *   $authorizer->authorize(new Article, 'create'); //=> boolean
 *   $authorizer->policy(new Article); //=> ArticlePolicy
 *   $authorizer->scope(new Article);  //=> ArticleScope
 *
 * Alternatively, some methods are exposed statically
 *
 *   Policy::scope(new Article); //=> ArticlePolicy
 *   Policy::policyOrFail(new ObjectWithoutPolicy); //=> NotDefinedException
 */
class Authorizer {

  use ProvidesAuthorization;

  /**
   * The current user
   *
   * @protected
   * @var mixed
   */
  protected $user;

  /**
   * Options to modify the context of the policy class
   *
   * @protected
   * @var array
   */
  protected $options;

  /**
   * List of methods on the trait to expose publicly
   *
   * @protected
   * @var array
   */
  protected $publicApi = [ 'authorize', 'policy', 'scope' ];



  /**
   * Configure the policy class with the current user and context
   *
   * @param  mixed  $user
   * @param  array  $options [optional]
   */
  public function __construct($user, array $options = []) {
    $this->user    = $user;
    $this->options = $options;
  }



  /**
   * {@inheritdoc}
   */
  protected function currentUser() {
    return $this->user;
  }



  /**
   * Magic `__callStatic` method, providing access to accessor methods on the
   * policy trait without the need to use the `get` prefix. For example,
   *
   *   Policy::scope(new Article); //=> ArticleScope
   *
   * @param  string  $method
   * @param  array   $parameters
   * @return mixed
   */
  public static function __callStatic($method, array $parameters) {
    $staticMethod = 'get' . ucfirst($method);

    if ( ! method_exists(get_class(), $staticMethod)) {
      throw new \BadMethodCallException(sprintf('A `%s` static method is not defined on `%s`.', $method, get_class()));
    }

    return call_user_func_array('static::' . $staticMethod, $parameters);
  }

  /**
   * Magice `_call` method, providing access to a specific subset of protected
   * methods defined on the policy trait
   *
   * @param  string  $method
   * @param  array   $parameters
   * @return mixed
   */
  public function __call($method, array $parameters) {
    if ( ! in_array($method, $this->publicApi)) {
      throw new \BadMethodCallException(sprintf('A `%s` method is not defined or exposed publicly on `%s`.', $method, get_class()));
    }

    return call_user_func_array([$this, $method], $parameters);
  }

}
