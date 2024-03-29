# Slim factory helper for Slim v4

![PHP from Packagist](https://img.shields.io/packagist/php-v/dakujem/slim-factory)
[![Test Suite](https://github.com/dakujem/slim-factory/actions/workflows/php-test.yml/badge.svg)](https://github.com/dakujem/slim-factory/actions/workflows/php-test.yml)
[![Coverage Status](https://coveralls.io/repos/github/dakujem/slim-factory/badge.svg?branch=main)](https://coveralls.io/github/dakujem/slim-factory?branch=main)

> 💿 `composer require dakujem/slim-factory`


## Usage

```php
use Dakujem\Slim\SlimFactory;
```

Build an App instance, optionally provide decorators and/or a container for the App instance:
```php
$app = SlimFactory::build();
$app = SlimFactory::build($decorators);
$app = SlimFactory::build($decorators, $container);
```

Or, build an App instance using core services from a container, with optional decorators:
```php
$app = SlimFactory::buildFromContainer($container);
$app = SlimFactory::buildFromContainer($container, $decorators);
```

In case you already have an instance od Slim `App`, your decorators can be used to decorate it:
```php
$app = Slim\Factory\AppFactory::create( ... );
SlimFactory::decorate($app, $decorators);
```

A **decorator** may be:
- an instance of `AppDecoratorInterface` implementation
- a string name of such a class
- a callable provider of such an instance **
- a callable that directly decorates the slim app instance ** 

```php
class MiddlewareDecorator implements AppDecoratorInterface
{
    public function decorate(App $slim): void
    {
        $slim->addRoutingMiddleware();
        $slim->addBodyParsingMiddleware();
        $slim->addErrorMiddleware();
    }
}

// The following 4 decorators are equivalent:
$decorators = [
    new MiddlewareDecorator(),          // a decorator instance
    MiddlewareDecorator::class,         // a class name
    fn() => new MiddlewareDecorator(),  // a decorator provider
    function(App $slim): void {         // a callable decorator
        $slim->addRoutingMiddleware();
        $slim->addBodyParsingMiddleware();
        $slim->addErrorMiddleware();
    }
];
```
>
> ** Note
>
> If a callable is used as a decorator, it is always invoked, regardless of its signature.
>
> If the callable returns an instance of a decorator (an implementation of `AppDecoratorInterface`),
> the returned decorator is immediately applied too.
>
> The callables receive the instance of the App being decorated as the first argument,
> which is the same signature as the one of `AppDecoratorInterface::decorate` method.
>


## Testing

Run unit tests using the following command:

`$` `composer test`


## Contributing

Ideas, feature requests and other contribution is welcome.
Please send a PR or create an issue.


