<?php
/**
 * Created by PhpStorm.
 * User: liuxiang
 * Date: 9/4/16
 * Time: 11:20 PM
 */

namespace RightCapital\FlySystem\Plugin;

use RightCapital\FlySystem\FilesystemInterface;

interface PluginInterface
{
    /**
     * Set the filesystem object.
     *
     * @param \RightCapital\FlySystem\FilesystemInterface $filesystem
     *
     */
    public function setFilesystem(FilesystemInterface $filesystem);

    /**
     * Get the method name
     *
     * @return string
     */
    public function getMethod();
}