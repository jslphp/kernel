<?php

namespace Jsl\Kernel\Modules;

use Jsl\Kernel\Kernel;
use Jsl\Kernel\Session\Session;
use Jsl\Kernel\Session\SessionInterface;
use Jsl\Router\Router;

class KernelModule extends AbstractModule
{
    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return 'Kernel';
    }


    /**
     * @inheritDoc
     */
    public function id(): string
    {
        return 'kernel';
    }


    /**
     * @inheritDoc
     */
    public function boot(Kernel $kernel): void
    {
        $kernel->router->setClassResolver(fn ($class) => $kernel->get($class));

        $kernel->bind(SessionInterface::class, function () {
            $session = new Session;

            if ($session->isStarted() === false) {
                $session->start();
            }

            return $session;
        });
    }


    /**
     * @inheritDoc
     */
    public function config(): array
    {
        return [
            'debug' => false,
            'timezone' => 'UTC',
        ];
    }
}
