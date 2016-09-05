<?php

namespace RightCapital\FlySystem\Plugin;

use RightCapital\FlySystem\FilesystemInterface;

abstract class AbstractPlugin implements PluginInterface
{
    /**
     * @var FilesystemInterface
     */
    protected $filesystem;

    /**
     * Set the filesystem
     *
     * @param \RightCapital\FlySystem\FilesystemInterface $filesystem
     */
    public function setFilesystem(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }
}