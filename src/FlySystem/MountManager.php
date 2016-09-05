<?php

namespace RightCapital\FlySystem;

use RightCapital\FlySystem\Plugin\PluggableTrait;
use RightCapital\FlySystem\Plugin\PluginNotFoundException;

class MountManager
{
    use PluggableTrait;

    /**
     * @var FilesystemInterface[] $filesystems
     */
    protected $filesystems = [];

    public function __construct(array $filesystems = [])
    {
        $this->mountFilesystems($filesystems);
    }

    /**
     * Mount filesystem
     *
     * @param $filesystems
     *
     * @return $this
     */
    public function mountFilesystems($filesystems)
    {
        foreach ($filesystems as $prefix => $filesystem) {
            $this->mountFilesystem($prefix, $filesystem);
        }

        return $this;
    }

    /**
     * Mount filesystem
     *
     * @param                                                       $prefix
     * @param \RightCapital\FlySystem\FilesystemInterface $filesystem
     *
     * @return $this
     */
    public function mountFileSystem($prefix, FilesystemInterface $filesystem)
    {
        if (!is_string($prefix)) {
            throw new \InvalidArgumentException(__METHOD__ . ' expects argument #1 to be a string.');
        }

        $this->filesystems[$prefix] = $filesystem;

        return $this;
    }

    private function filterPrefix(array $arguments)
    {
        if (empty($arguments)) {
            throw new \LogicException('At least one argument needed.');
        }

        $path = array_shift($arguments);

        if (!is_string($path)) {
            throw new \InvalidArgumentException('First argument shuold be a string.');
        }

        if (!preg_match('#^.+\:\/.*#', $path)) {
            throw new \InvalidArgumentException('No prefix detected in path: ' . $path);
        }

        list($prefix, $path) = explode('://', $path, 2);
        array_unshift($arguments, $path);

        return [$prefix, $arguments];
    }

    private function invokePluginOnFilesystem($method, $arguments, $prefix)
    {
        $filesystem = $this->getFilesystem($prefix);

        try {
            return $this->invokePlugin($method, $arguments, $filesystem);
        } catch (PluginNotFoundException $e) {

        }

        $callback = [$filesystem, $method];

        return call_user_func_array($callback, $arguments);
    }

    private function getFilesystem($prefix)
    {
        if (!isset($this->filesystems[$prefix])) {
            throw new \LogicException('No filesystem mounted with prefix.');
        }

        return $this->filesystems[$prefix];
    }

    private function invokePlugin($method, $arguments, $filesystem)
    {
        $plugin = $this->findPlugin($method);
        $plugin->setFilesystem($filesystem);
        $callback = [$plugin, 'handle'];

        return call_user_func_array($callback, $arguments);
    }



    public function __call(string $method, array $arguments)
    {
        list($prefix, $arguments) = $this->filterPrefix($arguments);

        return $this->invokePluginOnFilesystem($method, $arguments, $prefix);
    }


}

