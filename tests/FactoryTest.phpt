<?php

declare(strict_types=1);

namespace Dakujem\Slim\Test;

require_once __DIR__ . '/bootstrap.php';

use Dakujem\Sleeve;
use Dakujem\Slim\AppDecoratorInterface;
use Dakujem\Slim\FactoryException;
use Dakujem\Slim\SlimFactory;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\App;
use Slim\Psr7\Factory\ResponseFactory;
use Tester\Assert;
use Tester\TestCase;

class _MiddlewareDecorator implements AppDecoratorInterface
{
    public static $invoked;

    public function decorate(App $slim): void
    {
        (self::$invoked)($slim);
    }
}

/**
 * SlimFactory test.
 *
 * @see SlimFactory
 *
 * @author Andrej Rypak (dakujem) <xrypak@gmail.com>
 */
class _FactoryTest extends TestCase
{
    public function testDecorateMethodCallsAllDecoratorsWithCorrectArguments()
    {
        $i = 0;
        $decorators = [
            function (App $slim) use (&$i) {
                $i++;
            },
            function (App $slim) use (&$i) {
                $i++;
            },
            function (App $slim) use (&$i) {
                $i++;
            },
            function (App $slim) use (&$i) {
                $i++;
            },
        ];
        $slim = new App(new ResponseFactory());
        SlimFactory::decorate($slim, $decorators);
        Assert::same(4, $i);
    }

    public function testAcceptedDecoratorTypes()
    {
        $i = 0;
        _MiddlewareDecorator::$invoked = function () use (&$i) {
            $i++;
        };
        $decorators = [
            // a decorator instance
            new _MiddlewareDecorator(),
            // a class name
            _MiddlewareDecorator::class,
            // a decorator provider
            function () {
                return new _MiddlewareDecorator();
            },
            // a callable decorator
            function (App $slim) use (&$i): void {
                $i++;
            },
        ];
        $slim = new App(new ResponseFactory());
        SlimFactory::decorate($slim, $decorators);
        Assert::same(4, $i);
    }

    public function testThrows()
    {
        $slim = new App(new ResponseFactory());
        Assert::throws(function () use ($slim) {
            SlimFactory::decorate($slim, [
                42,
            ]);
        }, FactoryException::class);
        Assert::throws(function () use ($slim) {
            SlimFactory::decorate($slim, [
                'invalid string',
            ]);
        }, FactoryException::class);
        Assert::throws(function () use ($slim) {
            SlimFactory::decorate($slim, [
                SlimFactory::class, // existing class name but not a decorator
            ]);
        }, FactoryException::class);
    }

    public function testBuildMethodBuildsAnAppAndDecoratesIt()
    {
        $i = 0;
        $decorators = [
            function (App $slim) use (&$i) {
                $i++;
            },
            function (App $slim) use (&$i) {
                $i++;
            },
        ];
        Assert::type(App::class, SlimFactory::build($decorators));
        Assert::same(2, $i);

        $container = new Sleeve();
        $app = SlimFactory::build($decorators, $container);
        Assert::type(App::class, $app);
        Assert::same(4, $i);

        Assert::same($container, $app->getContainer(), 'The container is correctly assigned.');
    }

    public function testBuildFromContainerMethodBuildsAnAppAndDecoratesIt()
    {
        $i = 0;
        $decorators = [
            function (App $slim) use (&$i) {
                $i++;
            },
            function (App $slim) use (&$i) {
                $i++;
            },
        ];
        $container = new Sleeve();
        $responseFactory = new ResponseFactory();
        // this service will directly be used from the container
        $container[ResponseFactoryInterface::class] = $responseFactory;
        $app = SlimFactory::buildFromContainer($container, $decorators);
        Assert::type(App::class, $app);
        Assert::same(2, $i);
        Assert::same($container, $app->getContainer(), 'The container is correctly assigned.');
        Assert::same($responseFactory, $app->getResponseFactory(), 'The core services are taken from the container.');
    }
}

// run the test
(new _FactoryTest)->run();
