<?php
/**
 * Created by PhpStorm.
 * User: liuxiang
 * Date: 9/1/16
 * Time: 11:50 AM
 */

namespace RightCapital\FlySystem\FlySystem;

abstract class Handler
{
    /**
     * @var \RightCapital\FlySystem\FlySystem\Filesystem
     */
    private $fileSystem;
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
}