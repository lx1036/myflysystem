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
    public function write($path, $contents)
    {
        // TODO: Implement write() method.
    }

    public function update($path, $contents)
    {
        // TODO: Implement update() method.
    }

    public function rename($path, $newpath)
    {
        // TODO: Implement rename() method.
    }

    public function delete($path)
    {
        // TODO: Implement delete() method.
    }

    public function deleteDir($dirname)
    {
        // TODO: Implement deleteDir() method.
    }

    public function createDir($dirname)
    {
        // TODO: Implement createDir() method.
    }

    public function has($path)
    {
        // TODO: Implement has() method.
    }

    public function read($path)
    {
        // TODO: Implement read() method.
    }

    public function listContents()
    {
        // TODO: Implement listContents() method.
    }

    public function getMetadata($path)
    {
        // TODO: Implement getMetadata() method.
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
}