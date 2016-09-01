<?php
/**
 * Created by PhpStorm.
 * User: liuxiang
 * Date: 9/1/16
 * Time: 11:47 AM
 */

namespace RightCapital\FlySystem\FlySystem;

interface ReadInterface
{
    public function has($path);
    public function read($path);
    public function listContents();
    public function getMetadata($path);
    public function getSize($path);
    public function getMimetype($path);
    public function getTimestamp($path);
}