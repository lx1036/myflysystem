<?php

namespace RightCapital\FlySystem\FlySystem\Plugin;

trait PluggableTrait
{

    protected $plugins = [];
    /**
     * Find a specific plugin
     *
     * @param string $method
     *
     * @throws \LogicException
     *
     * @return PluginInterface $plugin
     */
    private function findPlugin($method)
    {
        if (!isset($this->plugins[$method])) {
            throw new PluginNotFoundException('Plugin not found for method:' . $method);
        }

        if (!method_exists($this->plugins[$method], 'handle')) {
            throw new \LogicException(get_class($this->plugins[$method]) . ' does not have a handle method.');
        }

        return $this->plugins[$method];
    }
}