<?php

namespace RightCapital\FlySystem;

use RightCapital\FlySystem\Plugin\PluggableTrait;
use RightCapital\FlySystem\Plugin\PluginNotFoundException;

/**
 * Class MountManager
 *
 * @package RightCapital\FlySystem
 *
 * @method bool delete($path)
 */
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
     * @param \RightCapital\FlySystem\FilesystemInterface           $filesystem
     *
     * @return $this
     */
    public function mountFilesystem($prefix, FilesystemInterface $filesystem)
    {
        if (!is_string($prefix)) {
            throw new \InvalidArgumentException(__METHOD__ . ' expects argument #1 to be a string.');
        }

        $this->filesystems[$prefix] = $filesystem;

        return $this;
    }

    /**
     * Get the filesystem by the prefix.
     *
     * @param $prefix
     *
     * @return \RightCapital\FlySystem\FilesystemInterface
     */
    public function getFilesystem($prefix)
    {
        if (!isset($this->filesystems[$prefix])) {
            throw new \LogicException('No filesystem mounted with prefix.');
        }

        return $this->filesystems[$prefix];
    }

    /**
     * @param array $arguments
     *
     * @return array
     */
    public function filterPrefix(array $arguments)
    {
        if (empty($arguments)) {
            throw new \LogicException('At least one argument needed.');
        }

        $path = array_shift($arguments);

        if (!is_string($path)) {
            throw new \InvalidArgumentException('First argument should be a string.');
        }

        if (!preg_match('#^.+\:\/.*#', $path)) {
            throw new \InvalidArgumentException('No prefix detected in path: ' . $path);
        }

        list($prefix, $path) = explode('://', $path, 2);
        array_unshift($arguments, $path);

        return [$prefix, $arguments];
    }

    /**
     * Copy resource between filesystems.
     *
     * @param       $from
     * @param       $to
     * @param array $config
     *
     * @return bool|mixed
     */
    public function copy($from, $to, array $config = [])
    {
        list($prefixFrom, $arguments) = $this->filterPrefix([$from]);
        $fsFrom = $this->getFilesystem($prefixFrom);
        $buffer = call_user_func_array([$fsFrom, 'readStream'], $arguments);

        if ($buffer === false) {
            return false;
        }

        list($prefixTo, $arguments) = $this->filterPrefix([$to]);
        $fsTo   = $this->getFilesystem($prefixTo);
        $result = call_user_func_array([$fsTo, 'writeStream'], array_merge($arguments, [$buffer, $config]));

        if (is_resource($buffer)) {
            fclose($buffer);
        }

        return $result;
    }

    /**
     * Move resource between filesystems.
     *
     * @param       $from
     * @param       $to
     * @param array $config
     *
     * @return bool
     */
    public function move($from, $to, array $config = [])
    {
        $copied = $this->copy($from, $to, $config);

        if ($copied) {
            return $this->delete($from);
        }

        return false;
    }

    /**
     * @param string $directory
     * @param bool   $recursive
     *
     * @return array
     */
    public function listContents($directory = '', $recursive = false)
    {
        list($prefix, $arguments) = $this->filterPrefix([$directory]);
        $filesystem = $this->getFilesystem($prefix);
        $directory  = array_shift($arguments);
        $result     = $filesystem->listContents($directory, $recursive);

        foreach ($result as &$file) {
            $file['filesystem'] = $prefix;
        }

        return $result;
    }

    /**
     * List plugin adapter.
     *
     * @param array  $keys
     * @param string $directory
     * @param bool   $recursive
     *
     * @return mixed
     */
    public function listWith(array $keys = [], $directory = '', $recursive = false)
    {
        list($prefix, $arguments) = $this->filterPrefix([$directory]);
        $directory = $arguments[0];
        $arguments = [$keys, $directory, $recursive];

        return $this->invokePluginOnFilesystem('listWith', $arguments, $prefix);
    }

    /**
     * Invoke a plugin on a filesystem mounted on a given prefix.
     *
     * @param $method
     * @param $arguments
     * @param $prefix
     *
     * @return mixed
     */
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

    public function __call($method, array $arguments)
    {
        list($prefix, $arguments) = $this->filterPrefix($arguments);

        return $this->invokePluginOnFilesystem($method, $arguments, $prefix);
    }
}

