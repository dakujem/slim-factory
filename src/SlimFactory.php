<?php

namespace Dakujem\Slim;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\MiddlewareDispatcherInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteResolverInterface;
use Traversable;

/**
 * Slim Factory.
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
final class SlimFactory
{
    /**
     * Create and configure an instance of Slim v4 App.
     *
     * @param iterable $decorators an iterable containing app decorators, see `SlimFactory::decorate()` for accepted values
     * @param ContainerInterface|null $container optionally specify a service container
     * @return App
     * @throws FactoryException
     */
    public static function build(iterable $decorators = [], ContainerInterface $container = null): App
    {
        return self::decorate(AppFactory::create(
            null, // let the AppFactory detect & configure the request factory
            $container
        // let the AppFactory provide the rest of the dependencies
        ), $decorators);
    }

    /**
     * Create an instance of Slim v4 App using core services from a container and configure it using decorators.
     *
     * The factory will try to fetch the following services from the container:
     * @see ResponseFactoryInterface
     * @see CallableResolverInterface
     * @see RouteCollectorInterface
     * @see RouteResolverInterface
     * @see MiddlewareDispatcherInterface
     *
     * @param ContainerInterface $container a service container containing core services for the App instance
     * @param iterable $decorators an iterable containing app decorators, see `SlimFactory::decorate()` for accepted values
     * @return App
     * @throws FactoryException
     */
    public static function buildFromContainer(ContainerInterface $container, iterable $decorators = []): App
    {
        return self::decorate(AppFactory::createFromContainer($container), $decorators);
    }

    /**
     * Configure given App instance using decorators.
     *
     * Each decorator can be one of:
     * - an instance of AppDecoratorInterface implementation
     * - a string name of such a class
     * - a callable provider of such an instance
     * - a callable that directly decorates the slim app instance (passed in as the first argument)
     *
     * @param App $slim
     * @param array|Traversable $decorators
     * @return App returns the same instance, decorated
     * @throws FactoryException
     */
    public static function decorate(App $slim, iterable $decorators): App
    {
        foreach ($decorators as $c) {
            $hasBeenInvoked = false;
            if (is_string($c) && class_exists($c)) {
                $c = new $c();
            } elseif (is_callable($c)) {
                $c = $c($slim);
                $hasBeenInvoked = true;
            }
            if ($c instanceof AppDecoratorInterface) {
                $c->decorate($slim);
            } elseif (!$hasBeenInvoked) {
                throw new FactoryException(sprintf(
                        'Improper Slim configurator, need an instance of %s or a callable, got %s.',
                        AppDecoratorInterface::class,
                        is_object($c) ? 'an instance of ' . get_class($c) : gettype($c))
                );
            }
        }
        return $slim;
    }
}
