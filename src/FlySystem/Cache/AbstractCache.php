<?php
/**
 * Created by PhpStorm.
 * User: liuxiang
 * Date: 9/1/16
 * Time: 12:49 PM
 */

namespace RightCapital\FlySystem\FlySystem\Cache;

use RightCapital\FlySystem\FlySystem\CacheInterface;
use RightCapital\FlySystem\FlySystem\Util;

abstract class AbstractCache implements CacheInterface
{
    protected $complete = false;
    protected $autosave = true;
    protected $cache    = [];

    public function __destruct()
    {
        if (!$this->autosave) {
            $this->save();
        }
    }

    public function isComplete()
    {
        return $this->complete;
    }

    public function setComplete($complete = true)
    {
        $this->complete = $complete;
        $this->autosave();

        return $this;
    }

    public function storeContents(array $contents)
    {
        foreach ($contents as $content) {
            $this->updateObject($content['path'], $content);
        }

        $this->setComplete();

        return $this->listContents();
    }

    public function updateObject($path, array $object, $autosave = false)
    {
        if (!isset($this->cache[$path])) {
            $this->cache[$path] = Util::pathinfo($path);
        }

        $this->cache[$path] = array_merge($this->cache[$path], $object);

        if ($autosave) {
            $this->autosave();
        }

        return $this->cache[$path];
    }

    public function flush()
    {
        // TODO: Implement flush() method.
    }

    public function autosave()
    {
        if ($this->autosave) {
            $this->save();
        }
    }

    public function rename($path, $newpath)
    {
        if (!$this->has($path)) {
            return;
        }

        $object = $this->cache[$path];
        unset($this->cache[$path]);
        $object['path'] = $newpath;
        $object = array_merge($object, Util::pathinfo($newpath));
        $this->cache[$newpath] = $object;

        $this->autosave();
    }

    public function delete($path)
    {
        if ($this->has($path)) {
            unset($this->cache[$path]);
        }

        $this->autosave();
    }

    public function deleteDir($dirname)
    {
        foreach ($this->cache as $path => $object) {
            if (strpos($path, $dirname) === 0) {
                unset($this->cache[$path]);
            }
        }

        $this->autosave();
    }

    public function has($path)
    {
        return isset($this->cache[$path]);
    }

    public function read($path)
    {
        return $this->cache[$path]['contents'] ?? null;
    }

    public function listContents()
    {
        return array_values($this->cache);
    }

    public function getSize($path)
    {
        return $this->cache[$path]['size'] ?? null;
    }

    public function getMimetype($path)
    {
        if (isset($this->cache[$path]['mimetype'])) {
            return $this->cache[$path]['mimetype'];
        }

        if ($contents = $this->read($path)) {
            $mimetype = Util::contentMimetype($contents);
            $this->cache[$path]['mimetype'] = $mimetype;

            return compact('mimetype');
        }
    }

    public function getMetadata($path)
    {
        if (isset($this->cache[$path]['filename'])) {
            return $this->cache[$path];
        }
    }

    public function getTimestamp($path)
    {
        return $this->cache[$path]['mimetype'] ?? null;
    }

}