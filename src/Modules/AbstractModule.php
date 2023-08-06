<?php

namespace Jsl\Kernel\Modules;

use Jsl\Kernel\Kernel;
use Jsl\Router\Contracts\RouterInterface;

/**
 * This class is just for convenience.
 */
abstract class AbstractModule implements ModuleInterface
{

    /**
     * @inheritDoc
     */
    public function boot(Kernel $kernel): void
    {
    }


    /**
     * @inheritDoc
     */
    public function routes(RouterInterface $router): void
    {
    }


    /**
     * @inheritDoc
     */
    public function config(): array
    {
        return [];
    }


    /**
     * @inheritDoc
     */
    public function deferred(Kernel $kernel): void
    {
    }
}
