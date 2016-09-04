<?php
/**
 * Created by PhpStorm.
 * User: liuxiang
 * Date: 9/1/16
 * Time: 1:40 PM
 */

namespace RightCapital\FlySystem\FlySystem\Adapter;

use RightCapital\FlySystem\FlySystem\Util;

class Local extends AbstractAdapter
{
    protected $root;

    public function __construct($root)
    {
        $this->root = Util::normalizePrefix(realpath($root), DIRECTORY_SEPARATOR);
    }

    public function has($path)
    {
        return file_exists($this->prefix($path));
    }

    public function write($path, $contents)
    {
        $location = $this->prefix($path);
        
        if ($dirname = dirname($path) !== '.') {
            $this->ensureDirectory($dirname);
        }

        $size = file_put_contents($location, $contents, LOCK_EX);

        return array_merge($pathinfo, [
            'contents' => $contents,
            'type'     => 'file',
        ]);
    }

    public function update($path, $contents)
    {
        // TODO: Implement update() method.
        $location = $this->prefix($path);

        return file_put_contents($location, $contents, LOCK_EX);
    }

    public function rename($path, $newpath)
    {
        // TODO: Implement rename() method.
        $location = $this->prefix($path);
        $destination = $this->prefix($newpath);

        return rename($location, $destination);
    }

    public function delete($path)
    {
        // TODO: Implement delete() method.
        return unlink($this->prefix($path));
    }

    public function deleteDir($dirname)
    {
        // TODO: Implement deleteDir() method.
    }

    public function createDir($dirname)
    {
        // TODO: Implement createDir() method.
    }

    public function read($path)
    {
        // TODO: Implement read() method.
    }

    public function listContents()
    {
        $paths = $this->directoryContents('', false);

        return array_map([$this, 'getMetadata'], $paths);
    }

    public function getMetadata($path)
    {
        $location = $this->prefix($path);
        $info = new \SplFileInfo($location);

        $meta['type'] = $info->getType();
        $meta['path'] = $path;
        
        if ($meta['type'] === 'file') {
            $meta['timestamp'] = $info->getMTime();
            $meta['size'] = $info->getSize();
        }

        return $meta;
    }

    public function getSize($path)
    {
        // TODO: Implement getSize() method.
    }

    public function getMimetype($path)
    {
        // TODO: Implement getMimetype() method.
    }

    public function getTimestamp($path)
    {
        // TODO: Implement getTimestamp() method.
    }

    private function prefix($path)
    {
    }

    private function ensureDirectory($dirname)
    {
    }

    private function directoryContents($string, $false)
    {
    }
}