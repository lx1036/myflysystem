<?php

namespace RightCapital\FlySystem\FlySystem\Plugin;

use RightCapital\FlySystem\FlySystem\FilesystemInterface;

abstract class AbstractPlugin implements PluginInterface
{
    /**
     * @var FilesystemInterface
     */
    protected $filesystem;

    /**
     * Set the filesystem
     *
     * @param \RightCapital\FlySystem\FlySystem\FilesystemInterface $filesystem
     */
    public function setFilesystem(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }
}