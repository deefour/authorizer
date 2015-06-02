# Authorizer

[![Build Status](https://travis-ci.org/deefour/authorizer.svg)](https://travis-ci.org/deefour/authorizer)
[![Packagist Version](http://img.shields.io/packagist/v/deefour/authorizer.svg)](https://packagist.org/packages/deefour/authorizer)
[![Code Climate](https://codeclimate.com/github/deefour/authorizer/badges/gpa.svg)](https://codeclimate.com/github/deefour/authorizer)

Simple Authorization via PHP Classes. Inspired by [elabs/**pundit**](https://github.com/elabs/pundit).

## Getting Started

Add Authorizer to your `composer.json` file and run `composer update`. See [Packagist](https://packagist.org/packages/deefour/authorizer) for specific versions.

```
"deefour/authorizer": "~0.5.0"
```

**`>=PHP5.5.0` is required.**

> **Note:** A work-in-progress attempt to explain how I use this package along with [`deefour/transformer`](https://github.com/deefour/transformer) and [`deefour/interactor`](https://github.com/deefour/interactor) to aide me in application development **[is available at this gist](https://gist.github.com/deefour/c6cfcebe808216a874f5)**.

## Policies

At the core of Authorizer is the notion of policy classes. A policy must extend `Deefour\Authorizer\Policy`. Each method should return a boolean. For example

```php
use Deefour\Authorizer\Policy;

class ArticlePolicy extends Policy {

  public function edit() {
    return $this->user->id === $this->record->author_id; // Only the article's author is allowed to edit it
  }

}
```

When a policy class is instantiated, the `$user` to authorize is provided along with a `$record` to authorize against. The `$record` must implement `Deefour\Authorizer\Contracts\Authorizable` to be "authorizable".

```php
$user    = User::find(1);
$article = $user->articles()->first();

$policy  = new ArticlePolicy($user, $article);

$policy->edit(); //=> true; the $user can edit the $article
```

### Mass Assignment Protection

A special `permittedAttributes` method can be created on a policy to conditionally provide a whitelist of attributes for a given request by a user to create or modify a record.

```php
use Deefour\Authorizer\Policy;

class ArticlePolicy extends Policy {

  public function permittedAttributes() {
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

### Closed System

Many apps only allow authenticated users to perform actions. Instead of verifying on every policy action that the current user is not `null`, unpersisted in the database, or similarly not a legitimate, authenticated user, create a base policy all others will extend

```php
namespace App\Policies;

use Deefour\Authorizer\Contracts\Authorizee as AuthorizeeContract;
use Deefour\Authorizer\Policy;
use Deefour\Authorizer\Exceptions\NotAuthorizedException;

class Policy extends Policy {

  public function __construct(AuthorizeeContract $user, $record) {
    if (is_null($user) or ! $user->exists) {
      throw new NotAuthorizedException('You must be logged in!');
    }

    parent::__construct($user, $record);
  }

}
```

## Scopes

Policy-based scopes are also supported. A policy scope must extend `Deefour\Authorizer\Scope` and will be required to implement a `resolve()` method. The return value will typically be an iterable collection of objects the current user is able to access. For example

```php
use Deefour\Authorizer\Scope;

class ArticleScope extends Scope {

  public function resolve() {
    if ($this->user->isAdmin()) {
      return $this->scope->all();
    } else {
      return $this->scope->where('published', true)->get();
    }
  }

}
```

When a scope class is instantiated, the `$user` to authorize is provided along with a `$scope` to manipulate.

```php
$user        = User::find(1);
$query       = Article::newQuery();

$policyScope = new ArticleScope($user, $query);

$policyScope->resolve(); //=> ALL Articles if the $user is an administrator; otherwise only published ones
```

## Authorizable Objects

Any PHP class can be used as the source object for which authorization will be performed as long as it implements `Deefour\Authorizer\Contracts\Authorizable`. This will require the following methods be defined on the object

 - `policyNamespace()`
 - `policyClass()`
 - `scopeClass()`

A default implementation for this interface is provided in the `Deefour\Authorizer\Authorizable` trait. A basic implementation for an authorizable object looks something like this

```php
use Deefour\Authorizer\Contracts\Authorizable as AuthorizableContract;
use Deefour\Authorizer\ResolvesPoliciesAndScopes;

class Article implements AuthorizableContract {

  use ResolvesPoliciesAndScopes;

}
```

### Policy & Scope Class Resolution

Some of the helper methods Authorizer provides automatically derive policy and scope class names based on the FQCN of the passed object. It does this by using the same namespace as the object, appending `'Policy'` or `'Scope'` to the object name. For example

```php
use Deefour\Authorizer\Authorizer;

$article    = new Article;
$nsArticle  = new Foo\Bar\Article;
$user       = User::find(1);

$authorizer = new Authorizer($user);

$authorizer->policy($user, $article);   //=> ArticlePolicy
$authorizer->policy($user, $nsArticle); //=> Foo\Bar\ArticlePolicy
```

This behavior can be overridden. Both of the `Article` classes above, regardless of their namespace, may share a single `Policies\ArticlePolicy` class. A `policyNamespace()` method can be implemented on both `Article` and `Foo\Bar\Article`.

```php
public function policyNamespace() {
  return 'Policies';
}
```

This will cause the following lookups to occur:

```php
use Deefour\Authorizer\Authorizer;

$article    = new Article;
$nsArticle  = new Foo\Bar\Article;
$user       = User::find(1);

$authorizer = new Authorizer($user);

$authorizer->policy($user, $article);   //=> Policies\ArticlePolicy
$authorizer->policy($user, $nsArticle); //=> Policies\ArticlePolicy
```

## Making Classes Aware of Authorization

The `Deefour\Authorizer\ProvidesAuthorization` trait can be included in any class to make working with policies and scopes easier. Using this trait requires implementing a `user()` method on the class.

```php
use Deefour\Authorizer\ProvidesAuthorization;

class ArticleController {

  use ProvidesAuthorization;

  protected function user() {
    return app('user') ?: new User;
  }

}
```

Within the context of the `ArticleController` class above, the policy class for an object can be generated with simply

```php
$object = new Article;

$this->policy($object); //=> ArticlePolicy
```

Scoping can be done with similar simplicity

```php
$query = Article::newQuery();

$this->scope($query); //=> Properly scoped collection of Articles via ArticleScope::resolve()
```

A failing authorization can trigger a loud response, throwing `Deefour\Authorizer\Exceptions\NotAuthorizedException`. This can short-circuit method execution with a single line of code.

```php
public function edit($id) {
  $object = Article::find($id);

  $this->authorize($object); //=> NotAuthorizedException will be thrown on failure

  echo "You can edit this article!"
}
```

If the current user is not allowed to edit the specified Article, the method execution will not make it to the `echo`.

### Assumptions Made by the API

Some assumptions are made by this Authorizer trait to provide you with the simple API described above.

When generating a policy class for an object, the following assumptions are made:

 1. The policy class is resolved by taking the FQCN of the object being authorized and appending `"Policy"` *( this can be overridden)*.
 2. The user the authorization is for is based on the return value of the `user()` method.

When generating a policy scope, the following assumptions are made:

 1. The policy class is resolved by taking the FQCN of the object being authorized and appending `"Scope"` *( this can be overridden)*.
 2. The user the authorization is for is based on the return value of the `user()` method.

When calling the `authorize()` method, a policy class is instantiated and the following assumptions are made:

 1. The policy method called is based on the name of the caller. If called within an `edit()` controller action, it will look for an `edit()` method on the policy class.
 2. If the user is not authorized for the action, Authorizer should fail loudly.

## Integration with Laravel

A base `App\Http\Controllers\Controller` controller in Laravel might look as follows with Authorizer integrated

```php
<?php namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Deefour\Authorizer\ProvidesAuthorization;
use App\User;

abstract class Controller extends BaseController {

  use ValidatesRequests;
  use ProvidesAuthorization;

  protected function user() {
    return app('auth')->user() ?: new User;
  }

}
```

All controllers extending this `App\Http\Controllers\Controller` are now aware of the functionality Authorizer provides.

### Service Provider

Authorizer comes with a service provider for `Deefour\Authorizer\Authorizer`. In Laravel's `config/app.php` file, add the `AuthorizationServiceProvider` to the list of providers.

```php
'providers' => [

  // ...

  'Deefour\Authorizer\Providers\AuthorizationServiceProvider',

],
```

The IoC container is responsible for instantiating a single, shared instance of the `Deefour\Authorizer\Authorizer` class. This is done outside the scope of a controller method, meaning the IoC container has no access to or knowledge of the `user()` method that may exist within a base controller. Because the API provided by the `Authorizer` does not expect a user to be passed, the service provider looks for configuration in an `app/config/authorizer.php` file on boot. At a minimum, the config must contain a callable `'user'` setting.

```php
<?php

return [

  'user' => function() {

    return Auth::user() ?: new User;

  },

];
```

To keep things DRY, the `user()` method in the base controller could be modified to take advantage of this same Closure.

```php
public function user() {
  return call_user_func(config('authorizer.user'));
}
```

The `Authorizer` can be accessed directly from the application container

```php
app('authorizer')->policy(new Article); //=> ArticlePolicy
```

or via typehinted methods resolved through the container, like controller actions

```php
use Deefour\Authorizer\Authorizer;
// ...

class ArticleController extends Controller {

  public function new(Authorizer $authorizer) {
    $authorizer->policy(new Article); //=> ArticlePolicy
  }

}
```

### Facade

The `Authorizer` generated via the IoC container can also be accessed via a facade by the same name. In Laravel's `config/app.php` file, add the `Authorizer` facade to the list of aliases.

Add the following to `app/config/app.php`

```php
'aliases' => [

  // ...

  'Authorizer' => 'Deefour\Authorizer\Facades\Authorizer',

],
```

and use the facade anywhere in your application

```php
Authorizer::policy(new Article); //=> ArticlePolicy
```

### Helper Methods

Global `authorizer()` and `policy()`, and `scope()` functions can be made globally available by including the `helpers.php` file in your project's `composer.json`. Authorizer doesn't autoload this file, giving you the choice whether or not to 'pollute' the global environment with these functions.

```php
"autoload": {
  "psr-4": {
    ...
  },
  "files": [
    "vendor/deefour/authorizer/src/helpers.php"
  ]
}
```

These helpers are particularly useful within views. For example, to conditionally show an 'Edit' link for a specific `$article` based on the current user's ability to edit that article

```php
@if (policy($article)->can('edit'))
  <a href="{{ URL::route('articles.edit', [ 'id' => $article->id ]) }}">Edit</a>
@endif
```

The `can()` method above is simply an alternative syntax to `policy($article)->edit()`.

### Gracefully Handling Unauthorized Exceptions

When a call to `authorize` fails, a `Deefour\Authorizer\Exceptions\NotAuthorizedException` exception is thrown. This can be caught by Laravel with a simple middleware.

```php
<?php namespace App\Http\Middleware;

use Closure;
use Deefour\Authorizer\Exceptions\NotAuthorizedException;
use Illuminate\Contracts\Routing\Middleware;

class HandleNotAuthorizedExceptionMiddleware implements Middleware {

  /**
   * Run the request filter.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
  */
  public function handle($request, Closure $next) {
    try {
      $response = $next($request);
    } catch (NotAuthorizedException $e) {
      return response('Unauthorized.', 401); // fail gracefully
    }

    return $response;
  }

}
```

### Ensuring Policies Are Used

An after filter can be configured to prevent actions missing authorization checks from being wide open by default. The following could be placed in a controller

```php
public function __construct() {
  $this->afterFilter(function() {
    $this->verifyAuthorized();
  }, [ 'except' => 'index' ]);
}
```

There is a similar method to ensure a scope is used, which is particularly useful for `index` actions where a collection of objects is rendered and is dependent on the current user's privileges.

```php
public function __construct() {
  $this->afterFilter(function() {
    $this->requirePolicyScoped();
  }, [ 'only' => 'index' ]);
}
```

### Helping Form Requests

Laravel's `Illuminate\Foundation\Http\FormRequest` class provides support for an `authorize()` method. Integrating policies into form request objects is easy. An added benefit is the validation rules can be based on authorization too:

```php
<?php namespace App\Http\Requests;

use Deefour\Authorizer\ProvidesAuthorization;
use Illuminate\Foundation\Http\FormRequest;

class CreateArticleRequest extends FormRequest {

  use ProvidesAuthorization;

  public function rules() {
    $rules = [
      'title' => 'required'
    ];

    if ( ! $this->policy->can('createWithoutApproval')) {
      $rules['approval_from'] => 'required';
    }

    return $rules;
  }

  public function authorize() {
    return $this->policy(new Article)->can('create');
  }

}
```

## Contribute

- Issue Tracker: https://github.com/deefour/authorizer/issues
- Source Code: https://github.com/deefour/authorizer

## Changelog

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

Copyright (c) 2014 [Jason Daly](http://www.deefour.me) ([deefour](https://github.com/deefour)). Released under the [MIT License](http://deefour.mit-license.org/).
