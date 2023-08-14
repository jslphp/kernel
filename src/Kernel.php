<?php

namespace Jsl\Kernel;

use Closure;
use Illuminate\Container\Container;
use Jsl\Config\Config;
use Jsl\Config\Contracts\ConfigInterface;
use Jsl\Kernel\Controllers\BaseController;
use Jsl\Kernel\Exceptions\KernelException;
use Jsl\Kernel\Modules\KernelModule;
use Jsl\Kernel\Modules\ModuleInterface;
use Jsl\Kernel\Modules\Registry as ModuleRegistry;
use Jsl\Kernel\Request\Request;
use Jsl\Kernel\Request\RequestInterface;
use Jsl\Kernel\Session\SessionInterface;
use Jsl\Router\Contracts\RouterInterface;
use Jsl\Router\Router;
use Symfony\Component\HttpFoundation\Response;

/**
 * @property SessionInterface $session
 */
class Kernel
{
    /**
     * @var ConfigInterface
     */
    public readonly ConfigInterface $config;

    /**
     * @var RequestInterface
     */
    public readonly RequestInterface $request;

    /**
     * @var RouterInterface
     */
    public readonly RouterInterface $router;

    /**
     * @var Container
     */
    protected Container $container;

    /**
     * @var ModuleRegistry
     */
    protected ModuleRegistry $modules;

    /**
     * @var Kernel|null
     */
    protected static ?Kernel $instance = null;

    /**
     * @var array<string>
     */
    protected array $configs = [];


    /**
     * @param array $configs List of config files to load
     */
    public function __construct(array $configs = [])
    {
        static::$instance ??= $this;

        $this->container = new Container;
        $this->container->instance($this::class, $this);
        $this->modules = new ModuleRegistry;
        $this->container->instance(ModuleRegistry::class, $this->modules);
        $this->config = new Config($configs);
        $this->container->instance(ConfigInterface::class, $this->config);
        $this->request = Request::createFromGlobals();
        $this->container->instance(RequestInterface::class, $this->request);
        $this->router = new Router;
        $this->container->instance(RouterInterface::class, $this->router);

        $this->setup();
        $this->addModule(KernelModule::class);
        $this->addModules($this->config->get('modules', []));

        BaseController::setKernel($this);
    }


    /**
     * Add a module
     *
     * @param string|ModuleInterface $module
     *
     * @return self
     */
    public function addModule(string|ModuleInterface $module): self
    {
        $module = $this->modules->add($module, $this);

        if ($module->id() !== null) {
            if ($module->config()) {
                // Add default config without replacing loaded user config
                $existing = $this->config->get($module->id(), []);
                $config = is_array($existing)
                    ? array_replace_recursive($module->config(), $existing)
                    : [];
                $this->config->add([$module->id() => $config]);
            }

            $module->routes($this->router);
        }

        return $this;
    }


    /**
     * Add list of modules
     *
     * @param array $modules
     *
     * @return self
     */
    public function addModules(array $modules): self
    {
        array_map([$this, 'addModule'], $modules);
        return $this;
    }


    /**
     * Check if a module has been registered or not
     *
     * @param string $moduleId
     *
     * @return bool
     */
    public function hasModule(string $moduleId): bool
    {
        return $this->modules->has($moduleId);
    }


    /**
     * Register a bind into the container
     *
     * @param string $abstract
     * @param Closure|string|null|null $concrete
     * @param bool $singleton
     *
     * @return self
     */
    public function bind(string $abstract, Closure|string|null $concrete = null, bool $singleton = true): self
    {
        $this->container->bind($abstract, $concrete, $singleton);
        return $this;
    }


    /**
     * Resolve the given type from the container
     *
     * @return mixed
     */
    public function get(string|callable $abstract, array $parameters = []): mixed
    {
        return $this->container->make($abstract, $parameters);
    }


    /**
     * Process the request and output the result
     */
    public function process()
    {
        $this->modules->deferred($this);
        $response = $this->router->run();

        if ($response instanceof Response) {
            $response->send();
            return;
        }

        echo $response;
    }


    /**
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name): mixed
    {
        switch ($name) {
            case 'session':
                return $this->get(SessionInterface::class);
                break;
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
                ' in ' . $trace[0]['file'] .
                ' on line ' . $trace[0]['line'],
            E_USER_NOTICE
        );

        return null;
    }


    /**
     * Get the first instantiated Kernel instance
     * 
     * @return Kernel
     */
    public static function getKernel(): Kernel
    {
        if (static::$instance === null) {
            throw new KernelException("An instance of the Kernel must be instantiated before you can retrieve it");
        }

        return static::$instance;
    }


    /**
     * Set up the environment based on the config
     *
     * @return void
     */
    protected function setup(): void
    {
        if ($this->config->get('kernel.debug') === true) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        }

        date_default_timezone_set($this->config->get('kernel.timezone', 'UTC'));
    }
}
