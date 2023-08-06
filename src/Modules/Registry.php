<?php

namespace Jsl\Kernel\Modules;

use Illuminate\Container\Container;
use Jsl\Kernel\Kernel;
use Jsl\Kernel\Modules\Exceptions\ModuleException;

class Registry
{
    /**
     * Pattern for validating module id's
     */
    const PATTERN_MODULE_ID = '/^([a-z][a-z0-9\_]+)$/i';

    /**
     * @var array<ModuleInterface>
     */
    protected array $modules = [];


    /**
     * Add a module
     *
     * @param string|ModuleInterface $module
     * @param Kernel $kernel
     *
     * @return ModuleInterface
     */
    public function add(string|ModuleInterface $module, Kernel $kernel): ModuleInterface
    {
        if (hasInterface($module, ModelIdentifier::class)) {
            throw new ModuleException("Modules must implement " . ModuleInterface::class);
        }

        if (is_string($module)) {
            $module = $kernel->get($module);
        }

        /**
         * @var ModuleInterface $module
         */
        $moduleId = $module->id();
        $class = $module::class;

        if ($moduleId !== null && preg_match(self::PATTERN_MODULE_ID, $moduleId) !== 1) {
            throw new ModuleException("Invalid module id '{$moduleId}' for module '{$class}'");
        }

        $moduleId = $moduleId ?? $class;
        if ($this->has($moduleId)) {
            throw new ModuleException("A module with the id {$moduleId} has already been added");
        }

        $this->modules[$moduleId] = $module;
        $module->boot($kernel);

        return $module;
    }


    /**
     * Check if a module has been registered or not
     *
     * @param string $moduleId
     *
     * @return bool
     */
    public function has(string $moduleId): bool
    {
        return key_exists($moduleId, $this->modules);
    }


    /**
     * Get a module
     *
     * @param string $moduleId
     *
     * @return ModuleInterface|null
     */
    public function get(string $moduleId): ?ModuleInterface
    {
        return $this->modules[$moduleId] ?? null;
    }


    /**
     * Execute all deferred module methods 
     *
     * @param Kernel $kernel
     * 
     * @return void
     */
    public function deferred(Kernel $kernel): void
    {
        array_map(fn ($mod) => $mod->deferred($kernel), $this->modules);
    }
}
