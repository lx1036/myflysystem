<?php

namespace RightCapital\FlySystem\FlySystem\Plugin;

use RightCapital\FlySystem\FlySystem\FilesystemInterface;

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

    public function addPlugin(PluginInterface $plugin)
    {
        $this->plugins[$plugin->getMethod()] = $plugin;

        return $this;
    }

    protected function invokePlugin($method, array $arguments, FilesystemInterface $filesystem)
    {
        $plugin = $this->findPlugin($method);
        $plugin->setFilesystem($filesystem);
        $callback = [$plugin, 'handle'];

        return call_user_func_array($callback, $arguments);
    }

    public function __call($method, array $arguments)
    {
        try {
            return $this->invokePlugin($method, $arguments, $this);
        } catch (PluginNotFoundException $e) {
            throw new \BadMethodCallException('Call to undefined method ' . get_class($this) . '::' . $method);
        }
    }
}