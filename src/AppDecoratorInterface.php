<?php

namespace Dakujem\Slim;

use Slim\App;

/**
 * Interface for decorator classes able to configure Slim App instances.
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
interface AppDecoratorInterface
{
    public function decorate(App $slim): void;
}
