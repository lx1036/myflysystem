<?php
/**
 * Created by PhpStorm.
 * User: liuxiang
 * Date: 9/1/16
 * Time: 11:48 AM
 */

namespace RightCapital\FlySystem;

interface CacheInterface extends ReadInterface
{
    public function isComplete();
    public function setComplete($complete = true);
    public function storeContents(array $contents);
    public function flush();
    public function autosave();
    public function save();
    public function load();
    public function rename($path, $newpath);
    public function delete($path);
    public function deleteDir($dirname);
}