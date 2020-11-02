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

A **decorator** may be:
- an instance of `AppDecoratorInterface` implementation
- a string name of such a class
- a callable provider of such an instance
- a callable that directly decorates the slim app instance (same signature as `AppDecoratorInterface::decorate`)

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



