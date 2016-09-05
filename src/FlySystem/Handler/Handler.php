<?php

namespace RightCapital\FlySystem\FlySystem\Handler;

use RightCapital\FlySystem\FlySystem\Filesystem;

abstract class Handler
{
    /**
     * @var \RightCapital\FlySystem\FlySystem\Filesystem
     */
    private $filesystem;
    /**
     * @var string
     */
    private $path;

    /**
     * Handler constructor.
     *
     * @param \RightCapital\FlySystem\FlySystem\Filesystem $fileSystem
     * @param string                                       $path
     */
    public function __construct(Filesystem $fileSystem, string $path)
    {
        $this->fileSystem = $fileSystem;
        $this->path       = $path;
    }

    public function isDir()
    {
        return $this->getType() === 'dir';
    }

    public function idFile()
    {
        return $this->getType() === 'file';
    }

    private function getType()
    {
        $metadata = $this->fileSystem->getMetadata($this->path);

        return $metadata['type'];
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path)
    {
        $this->path = $path;
    }

    /**
     * @return \RightCapital\FlySystem\FlySystem\Filesystem
     */
    public function getFilesystem(): Filesystem
    {
        return $this->filesystem;
    }

    /**
     * @param \RightCapital\FlySystem\FlySystem\Filesystem $filesystem
     */
    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

}