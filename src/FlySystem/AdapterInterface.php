<?php
/**
 * Created by PhpStorm.
 * User: liuxiang
 * Date: 9/1/16
 * Time: 11:46 AM
 */

namespace RightCapital\FlySystem\FlySystem;

interface AdapterInterface extends ReadInterface
{
    public function write($path, $contents);
    public function update($path, $contents);
    public function rename($path, $newpath);
    public function delete($path);
    public function deleteDir($dirname);
    public function createDir($dirname);
}