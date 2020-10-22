# Slim factory helper for Slim v4

> 💿 `composer require dakujem/slim-factory`


## Usage

```php
use Dakujem\Slim\SlimFactory;
```

Basic usage, optionally provide decorators and/or a container for the App instance:
```php
$app = SlimFactory::build();
$app = SlimFactory::build($decorators);
$app = SlimFactory::build($decorators, $container);
```

Or, build an App instance using services in a container, with optional decorators:
```php
$app = SlimFactory::buildFromContainer($container);
$app = SlimFactory::buildFromContainer($container, $decorators);
```

In case you already have an instance od Slim `App`, your decorators can be used to decorate it:
```php
$app = Slim\Factory\AppFactory::create( ... );
SlimFactory::decorate($app, $decorators);
```

A **decorator** is one of (see example below):
- an instance of `AppDecoratorInterface` implementation
- a string name of such a class
- a callable provider of such an instance
- a callable that directly decorates the slim app instance (same signature as `AppDecoratorInterface::decorate`)

```php
class MiddlewareDecorator implements AppDecoratorInterface
{
    public function configure(App $slim): void
    {
        $slim->addRoutingMiddleware();
        $slim->addBodyParsingMiddleware();
        $slim->addErrorMiddleware();
    }
}

// all the following 4 decorators do exacly the same:
$decorators = [
    new MiddlewareDecorator(),          // a decorator instance
    MiddlewareDecorator::class,         // a class name
    fn() => new MiddlewareDecorator(),  // a decorator provider
    function(App $slim): void {         // a decorator callable (no class)
        $slim->addRoutingMiddleware();
        $slim->addBodyParsingMiddleware();
        $slim->addErrorMiddleware();
    }
];
```



