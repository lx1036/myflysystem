<?php
/**
 * Created by PhpStorm.
 * User: liuxiang
 * Date: 9/1/16
 * Time: 11:51 AM
 */

namespace RightCapital\FlySystem\FlySystem;

class Filesystem
{
    /**
     * @var \RightCapital\FlySystem\FlySystem\AdapterInterface
     */
    private $adapter;
    /**
     * @var \RightCapital\FlySystem\FlySystem\CacheInterface
     */
    private $cache;

    public function __construct(AdapterInterface $adapter, CacheInterface $cache = null)
    {

        $this->adapter = $adapter;
        $this->cache = $cache ?: new Cache\Memory;
        $this->cache->load();
    }

    /**
     * Check wether a path exists
     *
     * @param string $path path to check
     *
     * @return bool wether the path exists
     */
    public function has(string $path): bool
    {
        if ($this->cache->has($path)) {
            return true;
        }

        if ($this->cache->isComplete() or $data = $this->adapter->has($path) === false) {
            return false;
        }

        $this->updateObject($path, $data === true ? [] : $data, true);

        return true;
    }

    public function write(string $path, $contents):bool
    {
        $this->assertAbsent($path);

        if (!$data = $this->adapter->write($path, $contents)) {
            return false;
        }

        $this->cache->updateObject($path, $data, true);
        $this->cache->ensureParentDirectories($path);

        return true;
    }

    public function update(string $path, $contents)
    {
        $this->assertPresent($path);
        if ($data = $this->adapter->update($path, $contents) === false) {
            return false;
        }

        $this->cache->updateObject($path, $data, true);

        return true;
    }

    public function read(string $path)
    {
        $this->assertPresent($path);

        if ($contents = $this->cache->read($path)) {
            return $contents;
        }

        if (!$data = $this->adapter->read($path)) {
            return false;
        }

        $this->cache->updateObject($path, $data, true);

        return $data['contents'];
    }

    public function rename($path, $newpath)
    {
        $this->assertPresent($path);
        $this->assertAbsent($path);
        $this->cache->rename($path, $newpath);

        return $this->adapter->rename($path, $newpath);
    }

    public function delete($path)
    {
        $this->assertPresent($path);
        $this->assertAbsent($path);

        return $this->adapter->delete($path);
    }

    public function deleteDir($dirname)
    {
        $this->cache->deleteDir($dirname);

        return $this->adapter->deleteDir($dirname);
    }

    public function listContents()
    {
        if ($this->cache->isComplete()) {
            return $this->cache->listContents();
        }

        $contents = $this->adapter->listContents();

        return $this->cache->storeContents($contents);
    }

    public function getMimetype($path)
    {
        $this->assertPresent($path);

        if ($mimetype = $this->cache->getMimetype($path)) {
            return $mimetype;
        }

        if (!$data = $this->adapter->getMimetype($path)) {
            return false;
        }

        $data = $this->cache->updateObject($path, $data, true);

        return $data['mimetype'];
    }

    public function getMetadata(string $path)
    {
        $this->assertPresent($path);

        if ($mimedata = $this->cache->getMimedata($path)) {
            return $mimedata;
        }

        if (!$mimedata = $this->adapter->getMetadata($path)) {
            return false;
        }

        $this->cache->updateObject($path, $mimedata, true);

        return $mimedata;
    }

    public function getHandler($path)
    {
        $metadata = $this->getMetadata($path);

        return $metadata['type'] === 'file' ? new File($this, $path) : new Directory($this, $path);
    }

    public function flushCache()
    {
        $this->cache->flush();

        return $this;
    }

    /**
     * Assert a file is present
     *
     * @param string $path  path to file
     * @throws FileNotFoundException
     */
    private function assertPresent($path)
    {
        if (!$this->has($path)) {
            throw new FileNotFoundException($path);
        }
    }

    /**
     * Assert a file is absent
     *
     * @param string $path  path to file
     * @throws FileExistsException
     */
    private function assertAbsent($path)
    {
        if ($this->has($path)) {
            throw new FileExistsException($path);
        }
    }
}