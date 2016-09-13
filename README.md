# Authorizer

[![Build Status](https://travis-ci.org/deefour/authorizer.svg)](https://travis-ci.org/deefour/authorizer)
[![Packagist Version](http://img.shields.io/packagist/v/deefour/authorizer.svg)](https://packagist.org/packages/deefour/authorizer)
[![Code Climate](https://codeclimate.com/github/deefour/authorizer/badges/gpa.svg)](https://codeclimate.com/github/deefour/authorizer)
[![License](https://poser.pugx.org/deefour/authorizer/license)](https://packagist.org/packages/deefour/authorizer)

Simple Authorization via PHP Classes. Inspired by [elabs/**pundit**](https://github.com/elabs/pundit).

## Getting Started

Run the following to add Authorizer to your project's `composer.json`. See [Packagist](https://packagist.org/packages/deefour/authorizer) for specific versions.

```bash
composer require deefour/authorizer
```

**`>=PHP5.6.0` is required.**

## Policies

At the core of Authorizer is the notion of policy classes. Policies accept a `$user` and `$record` during instantiation. Public methods (actions) contain logic to check if the `$user` can perform the action on the `$record`. Here is an example of a policy that authorizes users to create and edit article objects.

```php
class ArticlePolicy
{
    protected $user;

    protected $record;

    public function __construct($user, $record)
    {
        $this->user = $user;
        $this->record = $record;
    }

    public function create()
    {
        return $this->user->exists;
    }

    public function edit()
    {
        return $this->record->exists && $this->record->author->is($user);
    }
}
```

This policy allows any existing user to create a new article, and existing articles to be modified only by their author. Here are examples of how you might interact directly with this policy.

```php
(new ArticlePolicy($user, new Article))->create(); // => true
(new ArticlePolicy($user, Article::class))->create(); // => true
(new ArticlePolicy($user, new Article))->edit(); // => false
(new ArticlePolicy($user, $user->articles->first()))->edit(); // => true
```

### Mass Assignment Protection

A `permittedAttributes` method on a policy provides a whitelist of attributes for a request by a user when performing an action.

```php
class ArticlePolicy
{
    public function permittedAttributes()
    {
        $attributes = [ 'title', 'body', ];

        // prevent the author and slug from being modified after the article
        // has been persisted to the database.
        if ( ! $this->record->exists) {
            return array_merge($attributes, [ 'user_id', 'slug', ]);
        }

        return $attributes;
    }
}
```

Action-specific methods can also be provided by in the format `permittedAttributesFor{Action}`.

```php
class ArticlePolicy
{
    public function permittedAttributesForCreate()
    {
        return [ 'title', 'body', 'user_id', 'slug ];
    }

    public functoin permittedAttributesForEdit()
    {
        return [ 'title', 'body' ];
    }
}
```

## Scopes

Authorizer also provides support for retrieving a resultset restricted based on a user's ability through scopes. A scope object receives a `$user` and base `$scope` during instantiation. It is expected to implement a `resolve()` method with logic to refine the `$scope` and typically return an iterable collection of objects the current user is able to access. For example

```php
class ArticleScope
{
    protected $user;

    protected $scope;

    public __construct($user, $scope)
    {
        $this->user = $user;
        $this->scope = $scope;
    }

    public function resolve()
    {
        if ($this->user->isAdmin()) {
            return $this->scope->all();
        }

        return $this->scope->where('published', true)->get();
    }
}
```

This scope retrieves all articles if the current user is an administrator, and only published articles for other users.

```php
$user = User::first();
$query = Article::newQuery();

(new ArticleScope($user, $query))->resolve(); //=> iterable list of Article objects
```

## The Authorizer Object

Creating and working with policy and scope classes directly is fine, but there are easier ways to authorize user activity. The first is the `Deefour\Authorizer\Authorizer` class.

### Resolving Policies

A policy can be instantiated and returned based on a `$user` and `$record`.

```php
(new Authorizer)->policy(new User, Article::class); //=> ArticlePolicy
```

The policy resolution just appends `'Policy'` to the end of the `$record`'s class name by default. This can be customized by provided a static `policyClass` method on the `$record` class. For example, if the policy for `Article` is at `Policies\ArticlePolicy`, create a method like this:

```php
class Article
{
    static public function policyClass()
    {
        return \Policies\ArticlePolicy::class;
    }
}
```

> It's recommended that your `$record` objects extend a single class that implements a `policyClass` method that will work for most/all of your record classes instead of manually specifying FQN's on every record.

### Resloving Scopes

A scope can be instantiated and returned based on a `$user` and base `$scope`. Instead of returning a scope class, `Authorizer` will call `resolve()` on the scope class for you, returning the resultset.

```php
(new Authorizer)->scope(new User, new Article); //=> a scoped resultset
```

Similar to policy resolution, the scope resolution just appends `'Scope'` to the end of the `$scope` object by default. This can be customized by provided a static `scopeClass` method on the `$record` class.

```php
class Article
{
    static public function scopeClass()
    {
        return \Policies\ArticleScope::class;
    }
}
```

It's important to note that many times you will pass a partially built query object to the `scope()` method as the `$record` instead of an instance of a record that actually resolves to a scope class. For example, a more realistic example of the one above might look like this:

```php
(new Authorizer)->scope(new User, Article::where('promoted', true)); //=> ArticleScope
```

The second argument above will return an instance of `Illuminate\Database\Eloquent\Builder` instead of an instance of `Article`. Scope resolution will fail without a bit more help. The resolver must be told how to determine the actual record to resolve the scope from. This is done through a closure passed as an optional third argument which will be passed the `$scope` the authorizer receives.

```php
(new Authorizer)->scope(
    new User,
    Article::where('promoted', true),
    function ($scope) {
        return $scope->getModel();
    }
); //=> a scoped resultset
```

### Strict Resolution

If a policy or scope cannot be found, `null` will be returned. If you need to stop execution, call `policyOrFail()` or `scopeOrFail()` instead of simply `policy()` or `scope()`.

```php
(new Authorizer)->policyOrFail(new User, new Blog); //=> throws Deefour\Authorizer\Exception\NotDefinedException
```

### Authorization

The authorizer also provides an `authorize` method that receives a `$user`, `$record`, and `$action`. An exception will be thrown if anything but `true` is returned from the resolved policy's action method.

```php
(new Authorizer)->policyOrFail(new User, new Article, 'edit'); //=> throws Deefour\Authorizer\Exception\NotAuthorizedException
```

### Failure Reasons

Authorizer considers any value other than `true` returned from a policy action a failure. If a string is returned it will be passed through as the message on the thrown `NotAuthorizedException`. This message can be used to inform a user exactly why their attempt to perform action was denied.

```php
class ArticlePolicy
{
    public function edit()
    {
        if ($this->record->user->is($this->user)) {
            return true;
        }

        return 'You are not the owner of this article.';
    }
}
```

```php
try {
    (new Authorizer)->authorize(new User, new Article, 'edit');
} catch (NotAuthorizedException $e) {
    echo $e->getMessage(); //=> 'You are not the owner of this article.'
}
```

### Closed System

Many apps only allow users to perform actions while authenticated. Instead of verifying on every policy action that the current user is logged in, you can create a base policy all others extend.

```php
abstract class Policy
{
    public function __construct($user, $record)
    {
        if (is_null($user) or ! $user->exists) {
            throw new NotAuthorizedException($record, $this, 'initalization', 'You must be logged in!');
        }

        parent::__construct($user, $record);
    }
}
```

## Making Classes Aware of Authorization

In addition to the `Authorizer` class, a `Deefour\Authorizer\ProvidesAuthorization` trait is also provided to make authorizing user activity easier.

### Preparing for Authorization

This trait can be used in any class provided it overrides the following three `protected` methods on the implementing class:

#### `authorizerUser()`

This should return the user object to authorize. It can be useful to return a new/fresh/empty user object if no logged in user is present.

#### `authorizerAction()`

This should return the name of the action on the policy to be called. Often this is based on the controller method handling the current request.

#### `authorizerAttributes()`

This should return an array of input data for the request. This only needs to be overridden if you are taking advantage of the mass assignment protection.

### Usage

#### Retrieving Policies

With this trait included, a policy can be retrieved from within the controller. The `$user` needed for the policy instantiation is derived from the `authorizerUser()` method override.

```php
$this->policy(new Article); //=> ArticlePolicy
```

#### Retrieving Scopes

Scoping can be done with similar simplicity. Similar to the `Authorizer` class, this will call `resolve()` on the scope for you, returning the resultset. A closure is provided below returning the `$record` which the scope class should be resolved from based on the passed base `$scope`.

```php
$this->scope(
  Article::newQuery(),
  function($scope) {
      return $scope->getModel();
  }
); //=> a scoped resultset
```

Like policy resolution, the `$user` needed for the policy instantiation is derived from the `authorizerUser()` method override.


#### Authorization Checks

A failing authorization check will throw an instance of `Deefour\Authorizer\Exception\NotAuthorizedException`. This can short-circuit method execution with a single line of code.

```php
public function edit(Article $article)
{
    $this->authorize($article); //=> NotAuthorizedException will be thrown on failure

    echo "You can edit this article!"
}
```

Similar to policies, the `$user` and `$action` needed for the scope instantiation are derived from the `authorizerUser()` and `authorizerAction()` method overrides. An action can be passed as a second argument to call a specific method on the policy instead of the one `authorizerAction()` will return.

```php
$this->authorize($article, 'modify');
```

#### Mass Assignment

Model attributes can be safely mass assigned too. Calling `permittedAttributes()` will pull a whitelist of attributes from the request info returned from the `authorizerAttributes()` method. A policy is instantiated for the `$record` behind the scenes, again with the `$user` and `$action` needed being derived from the `authorizerUser()` and `authorizerAction()` method overrides.

```php
public function update(Article $article)
{
  $article->forceFill($this->permittedAttributes(new Article))->save();
}
```

A second argument can be provided to `permittedAttributes()` to call a specific variant of the method on the policy if available.



### Authorization Within Laravel

Integrating this library into a Laravel application is very straightforward.

#### Implementing the Trait Method Overrides

Within a Laravel application, an implementation satisfying the above overrides might look like this:

```php
use App\User;
use Auth;
use Deefour\Authorizer\ProvidesAuthorization;
use Illuminate\Routing\Controller as BaseController;
use Request;
use Route;

class Controller extends BaseController
{
    use ProvidesAuthorization;

    protected function authorizerAction()
    {
        $action = Route::getCurrentRoute()->getActionName();

        return substr($action, strpos($action, '@') + 1);
    }

    protected function authorizerUser()
    {
        return Auth::user() ?: new User;
    }

    protected function authorizerAttributes()
    {
        return Request::all();
    }
}
```

#### Gracefully Handling Unauthorized Exceptions

When a call to `authorize()` fails, a `Deefour\Authorizer\NotAuthorizedException` exception is thrown. Your Laravel app's `App\Exceptions\Handler` could be modified to support this exception.

 1. Add `Deefour\Authorizer\Exception\NotAuthorizedException:class` to the `$dontReport` list.
 2. Import `Deefour\Authorizer\Exception\NotAuthorizedException` at the top of the file.
 3. Make your `prepareException()` method look like this:

    ```php
   protected function prepareException(Exception $e)
    {
        if ($e instanceof NotAuthorizedException) {
           return new HttpException(403, $e->getMessage());
        }

        return parent::prepareException($e);
    }
    ```

#### Ensuring Policies Are Used

An middleware can be provided on a controller's constructor as a closure to prevent actions missing authorization checks from being wide open by default.

```php
public function __construct()
{
    $this->middleware(function ($request, $next) {
      $response = $next($request);

      $this->verifyAuthorized();

      return $response;
    });
}
```

This will throw a `Deefour\Authorizer\Exceptions\AuthorizationNotPerformedException` exception if the controller action is run without a call to `authorize()`.

There is a `verifyScoped` method to ensure a scope is used that will throw a `Deefour\Authorizer\Exceptions\ScopingNotPerformedException` if the controller action is run without a call to `scope()`.

On occasion, bypassing this blanket authorization or scoping requirement may be necessary. Exceptions will not be thrown if `skipAuthorization()` or `skipScoping()` are called before the verification occurs.

### Helping Form Requests

Laravel's `Illuminate\Foundation\Http\FormRequest` class has an `authorize()` method. Integrating policies into form request objects is easy. An added benefit is the validation rules can be based on authorization too:

```php
namespace App\Http\Requests;

use Deefour\Authorizer\ProvidesAuthorization;
use Illuminate\Foundation\Http\FormRequest;

class CreateArticleRequest extends FormRequest
{
    use ProvidesAuthorization;

    public function authorize()
    {
        return $this->authorize(new Article);
    }

    public function rules()
    {
        $rules = [
            'title' => 'required'
        ];

        if ( ! $this->policy->createWithoutApproval()) {
            $rules['approval_from'] => 'required';
        }

        return $rules;
    }

    protected authorizerUser()
    {
        return $this->user();
    }

    protected authorizerAttributes()
    {
        return $this->all();
    }

    protected authorizerAction()
    {
        return $this->has('id') ? 'create' : 'edit';
    }
}
```

## Contribute

- Issue Tracker: https://github.com/deefour/authorizer/issues
- Source Code: https://github.com/deefour/authorizer

## Changelog

#### 2.0.0 - September 13, 2016

 - Complete rewrite
 - Much of the API is the same, but many interfaces and base classes have been removed for simplicity
 - Laravel-specific global functions, facade, and service provider have been removed
 - Class resolution has been simplified (no more dependence on [deefour/producer](https://github.com/deefour/producer))

#### 1.1.0 - January 14, 2016

 - The `Authorizer` now does a strict type check. A `NotAuthorizedException` unless `true` is returned. Other 'truthy' values will fail authorization.
 - A string returned from a policy will now be set as the 'reason' for the authorization failure.

#### 1.0.0 - October 7, 2015

 - Release 1.0.0.
 - New `skipAuthorization()` and `skipScoping()` methods have been added
 to bypass the exception throwing of the verification API.

#### 0.6.0 - August 8, 2015

 - Large rewrite of the policy and scope resolver, now using [`deefour/producer`](https://github.com/deefour/producer).
 - The `policyNamespace()`, `policyClass()`, `scopeNamespace()` and `scopeClass()` methods have all been removed in favor of a single `resolve()` method now, used by the `deefour/producer` resolver.
 - Policies now **require** an `Authorizee` be passed to the constructor.

#### 0.5.2 - July 31, 2015

 - Throw `403` instead of `401` when unauthorized.

#### 0.5.1 - June 5, 2015

 - Now following PSR-2.

#### 0.5.0 - June 2, 2015

 - All static methods are now public instance methods.
 - Changed `currentUser()` to `user()` for simplicity and compatibility with Laravel.
 - Code cleaning.

#### 0.4.0 - March 25, 2015

 - New `ResolvesAuthorizable` interface. This can be used on a class such as the decorators in [`deefour/presenter`](https://github.com/deefour/presenter) to map an authorization attempt back to the underlying model, since the presenter itself is not implementing the `Authorizable` interface.
 - Now requires `symfony/http-kernel` to throw a full HTTP exception when authorization fails.
 - Code formatting improved.

#### 0.3.0 - March 19, 2015

 - Adding much improved support for policy scopes
 - Remove `helpers.php` from Composer autoload. Developers should be able to choose whether these functions are included.
 - Cleaned up docblocks.

#### 0.2.0 - February 4, 2015

 - Adding `Authorizee` contract to be attached to a `User` model for easy lookup through service containers.
 - Class Reorganization.
 - Fixes for the Laravel service provider.

#### 0.1.0 - November 13, 2014

 - Initial release independent of [deefour/Aide](https://github.com/deefour/aide).

## License

Copyright (c) 2016 [Jason Daly](http://www.deefour.me) ([deefour](https://github.com/deefour)). Released under the [MIT License](http://deefour.mit-license.org/).
0Looking
