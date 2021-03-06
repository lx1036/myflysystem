<?php
/**
 * Created by PhpStorm.
 * User: liuxiang
 * Date: 9/1/16
 * Time: 11:47 AM
 */

namespace RightCapital\FlySystem;

interface ReadInterface
{
    /**
     * Check if the file exists.
     *
     * @param \RightCapital\FlySystem\string $path
     *
     * @return array|bool|null
     */
    public function has($path);

    /**
     * Read a file.
     *
     * @param \RightCapital\FlySystem\string $path
     *
     * @return array|false
     */
    public function read($path);

    /**
     * Read a file as a stream.
     *
     * @param \RightCapital\FlySystem\string $path
     *
     * @return array|false
     */
    public function readStream($path);

    /**
     * List contents of a directory.
     *
     * @param string     $directory
     * @param bool|false $recursive
     *
     * @return array
     */
    public function listContents($directory = '', $recursive = false);

    /**
     * Get all the meta data of a file or directory.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getMetadata($path);

    /**
     * Get the file size.
     *
     * @param string $path
     *
     * @return int
     */
    public function getSize($path);

    /**
     * Get the mimetype of a file.
     *
     * @param \RightCapital\FlySystem\string $path
     *
     * @return array|false
     */
    public function getMimetype($path);

    /**
     * Get the timestamp of a file.
     *
     * @param \RightCapital\FlySystem\string $path
     *
     * @return array|false
     */
    public function getTimestamp($path);

    /**
     * Get the visibility of a file.
     *
     * @param \RightCapital\FlySystem\string $path
     *
     * @return array|false
     */
    public function getVisibility($path);
}