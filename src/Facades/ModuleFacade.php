<?php

namespace Moloni\Facades;

use Module;

/**
 * @see https://devdocs.prestashop-project.org/8/modules/creation/module-translation/classic-system/#other-classes
 */
class ModuleFacade
{
    /**
     * Module instance
     *
     * @var Module|null|false
     */
    private static $module;

    private static function loadModule(): void
    {
        if (empty(self::$module)) {
            self::$module = Module::getInstanceByName('moloni');
        }
    }

    public static function setModule(Module $module): void
    {
        self::$module = $module;
    }

    public static function getModule(): ?Module
    {
        self::loadModule();

        return self::$module;
    }
}
