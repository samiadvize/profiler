<?php

namespace Iadvize\Profiler;

use Zend\ModuleManager\Feature\ConfigProviderInterface;

/**
 * Redis Module loader
 *
 * @package Iadvize\Profiler
 */
class Module implements ConfigProviderInterface
{
    /**
     * Get the module config
     *
     * @return mixed
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}

