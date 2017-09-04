<?php

namespace Flymyshop\Core;

use Flymyshop\Containers\HookContainer;
use Flymyshop\Helpers\PluginHelper;

/**
 * Class EnablePlugins
 * Enable plugins that are independent of time of execution.
 */
class EnablePlugins
{
    /**
     * EnablePlugins constructor.
     */
    public function __construct()
    {
        $hookContainer = HookContainer::instance(); // getting HookContainer instance
        $pluginNames = new PluginHelper(); // getting list of all plugins
        $plugins = $pluginNames->getEnabledPlugins();

        foreach ($plugins as $plugin) {
            $plugin = (array) $plugin;
            $reflector = new \ReflectionClass('Flymyshop\Plugins\\'.$plugin['name'].'\\'.$plugin['name']);
            $main = $reflector->getMethod('main');
            $methods = $reflector->getMethods();
            foreach ($methods as $method) {
                if ($method->name == 'i_order_hook') {
                    $hookContainer->setHook(['i_order_hook' => $reflector->getName()]);
                }
            }
            $main->invoke(null); //invoke main() method
        }
    }
}
