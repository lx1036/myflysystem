<?php
/**
 * Created by PhpStorm.
 * User: liuxiang
 * Date: 9/1/16
 * Time: 11:51 AM
 */

namespace RightCapital\FlySystem\FlySystem;

use InvalidArgumentException;
use RightCapital\FlySystem\FlySystem\Config\ConfigAwareTrait;
use RightCapital\FlySystem\FlySystem\Exception\FileExistsException;
use RightCapital\FlySystem\FlySystem\Exception\FileNotFoundException;
use RightCapital\FlySystem\FlySystem\Exception\RootViolationException;
use RightCapital\FlySystem\FlySystem\Handler\Directory;
use RightCapital\FlySystem\FlySystem\Handler\File;
use RightCapital\FlySystem\FlySystem\Handler\Handler;
use RightCapital\FlySystem\FlySystem\Plugin\PluggableTrait;
use RightCapital\FlySystem\FlySystem\Support\ContentListingFormatter;

class Filesystem implements FilesystemInterface
{
    use PluggableTrait, ConfigAwareTrait;
    /**
     * @var \RightCapital\FlySystem\FlySystem\AdapterInterface
     */
    private $adapter;

    public function __construct(AdapterInterface $adapter, $config = null)
    {
        $this->adapter = $adapter;
        $this->setConfig($config);
    }

    /**
     * Check wether a path exists
     *
     * @param string $path path to check
     *
     * @return bool wether the path exists
     */
    public function has($path): bool
    {
        $path = Util::normalizePath($path);

        return strlen($path) === 0 ? false : (bool) $this->getAdapter()->has($path);
    }

    /**
     * @return \RightCapital\FlySystem\FlySystem\AdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    public function write($path, $contents, array $config = []):bool
    {
        $path = Util::normalizePath($path);
        $this->assertAbsent($path);
        $config = $this->prepareConfig($config);

        return (bool) $this->getAdapter()->write($path, $contents, $config);
    }

    public function update($path, $contents, array $config = [])
    {
        $path   = Util::normalizePath($path);
        $config = $this->prepareConfig($config);
        $this->assertPresent($path);

        return (bool) $this->getAdapter()->update($path, $contents, $config);
    }

    public function read($path)
    {
        $path = Util::normalizePath($path);
        $this->assertPresent($path);

        if (!$object = $this->getAdapter()->readStream($path)) {
            return false;
        }

        return $object['stream'];
    }

    public function rename($path, $newpath)
    {
        $path    = Util::normalizePath($path);
        $newpath = Util::normalizePath($newpath);
        $this->assertPresent($path);
        $this->assertAbsent($path);

        return (bool) $this->getAdapter()->rename($path, $newpath);
    }

    public function copy($path, $newpath)
    {
        $path    = Util::normalizePath($path);
        $newpath = Util::normalizePath($newpath);
        $this->assertPresent($path);
        $this->assertAbsent($newpath);

        return $this->getAdapter()->copy($path, $newpath);
    }

    public function delete($path)
    {
        $path = Util::normalizePath($path);
        $this->assertPresent($path);

        return $this->getAdapter()->delete($path);
    }

    public function deleteDir($dirname)
    {
        $dirname = Util::normalizePath($dirname);

        if ($dirname === '') {
            throw new RootViolationException('Root directories can not be deleted.');
        }

        return (bool) $this->adapter->deleteDir($dirname);
    }

    public function createDir($dirname, array $config = [])
    {
        $dirname = Util::normalizePath($dirname);
        $config  = $this->prepareConfig($config);

        return (bool) $this->getAdapter()->createDir($dirname, $config);
    }

    public function listContents($directory = '', $recursive = false)
    {
        $directory = Util::normalizePath($directory);
        $contents  = $this->getAdapter()->listContents($directory, $recursive);

        return (new ContentListingFormatter($directory, $recursive))->formatListing($contents);
    }

    public function getMimetype($path)
    {
        $path = Util::normalizePath($path);
        $this->assertPresent($path);

        if (!$object = $this->getAdapter()->getMimetype($path)) {
            return false;
        }

        return $object['mimetype'];
    }

    public function getMetadata($path)
    {
        $path = Util::normalizePath($path);
        $this->assertPresent($path);

        return $this->getAdapter()->getMetadata($path);
    }

    /**
     * Assert a file is present
     *
     * @param string $path path to file
     *
     * @throws FileNotFoundException
     */
    private function assertPresent($path)
    {
        if ($this->config->get('disable_asserts', false) === false && !$this->has($path)) {
            throw new FileNotFoundException($path);
        }
    }

    /**
     * Assert a file is absent
     *
     * @param string $path path to file
     *
     * @throws FileExistsException
     */
    private function assertAbsent($path)
    {
        if ($this->config->get('disable_asserts', false) === false && $this->has($path)) {
            throw new FileExistsException($path);
        }
    }

    public function readStream($path)
    {
        $path = Util::normalizePath($path);
        $this->assertPresent($path);

        if ( ! $object = $this->getAdapter()->readStream($path)) {
            return false;
        }

        return $object['stream'];
    }

    public function getSize($path)
    {
        $path = Util::normalizePath($path);

        if (($object = $this->getAdapter()->getSize($path)) === false || ! isset($object['size'])) {
            return false;
        }

        return (int) $object['size'];
    }

    public function getTimestamp($path)
    {
        $path = Util::normalizePath($path);
        $this->assertPresent($path);

        if ( ! $object = $this->getAdapter()->getTimestamp($path)) {
            return false;
        }

        return $object['timestamp'];
    }

    public function getVisibility($path)
    {
        $path = Util::normalizePath($path);
        $this->assertPresent($path);

        if (($object = $this->getAdapter()->getVisibility($path)) === false) {
            return false;
        }

        return $object['visibility'];
    }

    public function writeStream($path, $resource, array $config = [])
    {
        if ( ! is_resource($resource)) {
            throw new InvalidArgumentException(__METHOD__ . ' expects argument #2 to be a valid resource.');
        }

        $path = Util::normalizePath($path);
        $this->assertAbsent($path);
        $config = $this->prepareConfig($config);

        Util::rewindStream($resource);

        return (bool) $this->getAdapter()->writeStream($path, $resource, $config);
    }

    public function updateStream($path, $resource, array $config = [])
    {
        // TODO: Implement updateStream() method.
    }

    public function setVisibility($path, $visibility)
    {
        $path = Util::normalizePath($path);

        return (bool) $this->getAdapter()->setVisibility($path, $visibility);
    }

    /**
     * Update or write a file.
     *
     * @param string $path
     * @param string $contents
     * @param array  $config
     *
     * @return bool
     */
    public function put($path, $contents, array $config = [])
    {
        $path = Util::normalizePath($path);
        $config = $this->prepareConfig($config);

        if ($this->has($path)) {
            return (bool) $this->getAdapter()->update($path, $contents, $config);
        }

        return (bool) $this->getAdapter()->write($path, $contents, $config);
    }

    public function putStream($path, $resource, array $config = [])
    {
        if (!is_resource($resource)) {
            throw new InvalidArgumentException(__METHOD__ . ' expects argument #2 to be a valid resource.');
        }

        $path = Util::normalizePath($path);
        $config = $this->prepareConfig($config);
        Util::rewindStream($resource);

        if ($this->has($path)) {
            return (bool) $this->getAdapter()->updateStream($path, $resource, $config);
        }

        return (bool) $this->getAdapter()->writeStream($path, $resource, $config);
    }

    public function readAndDelete($path)
    {
        $path = Util::normalizePath($path);
        $this->assertPresent($path);
        $contents = $this->read($path);

        if ($contents === false) {
            return false;
        }

        $this->delete($path);

        return $contents;
    }

    public function get($path, Handler $handler = null)
    {
        $path = Util::normalizePath($path);

        if (empty($handler)) {
            $metadata = $this->getMetadata($path);
            $handler = $metadata['type'] === 'file' ? new File($this, $path) : new Directory($this, $path);
        }

        $handler->setPath($path);
        $handler->setFilesystem($this);

        return $handler;
    }
}