<?php

namespace Jsl\Kernel\Controllers;

use Jsl\Common\Views\ViewsInterface;
use Jsl\Kernel\Kernel;
use Jsl\Router\Contracts\RouterInterface;

abstract class BaseController
{
    /**
     * @var Kernel
     */
    protected static Kernel $kernel;


    /**
     * @param Kernel $kernel
     *
     * @return void
     */
    public static function setKernel(Kernel $kernel): void
    {
        static::$kernel = $kernel;
    }


    /**
     * @param string $template
     * @param array $data
     *
     * @return string
     */
    public function render(string $template, array $data = []): string
    {
        return static::$kernel->get(ViewsInterface::class)
            ->render($template, $data);
    }


    /**
     * Get a named route
     *
     * @param string $name
     * @param array $arguments
     *
     * @return string
     */
    public function route(string $name, array $arguments = []): string
    {
        return static::$kernel->get(RouterInterface::class)
            ->getNamedRoute($name, $arguments);
    }
}
