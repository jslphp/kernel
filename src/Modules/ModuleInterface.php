<?php

namespace Jsl\Kernel\Modules;

use Jsl\Kernel\Kernel;
use Jsl\Router\Contracts\RouterInterface;

interface ModuleInterface
{
    /**
     * The name of the current module
     *
     * @return string
     */
    public function name(): string;


    /**
     * The key of the current module (only aplhanum, _, -, are allowed)
     * - If it returns null, no config or routes for it will be added
     *
     * @return string|null
     */
    public function id(): ?string;


    /**
     * Called when the module is added
     *
     * @param Kernel $kernel
     *
     * @return void
     */
    public function boot(Kernel $kernel): void;


    /**
     * Called when the router is initialized
     *
     * @param RouterInterface $router
     *
     * @return void
     */
    public function routes(RouterInterface $router): void;


    /**
     * Return the modules configuration
     *
     * @return array
     */
    public function config(): array;


    /**
     * Called when the kernel starts
     *
     * @param Kernel $kernel
     *
     * @return void
     */
    public function deferred(Kernel $kernel): void;
}
