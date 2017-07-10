<?php

namespace RightCapital\FlySystem\Adapter;

use RightCapital\FlySystem\Config\Config;
use RightCapital\FlySystem\ReadInterface;

interface AdapterInterface extends ReadInterface
{
    /**
     * @const  VISIBILITY_PUBLIC  public visibility
     */
    const VISIBILITY_PUBLIC = 'public';

    /**
     * @const  VISIBILITY_PRIVATE  private visibility
     */
    const VISIBILITY_PRIVATE = 'private';

    /**
     * Write a new file.
     *
     * @param                                                 $path
     * @param                                                 $contents
     * @param \RightCapital\FlySystem\Config\Config           $config
     *
     * @return array|false
     */
    public function write($path, $contents, Config $config);

    /**
     * Write a new file using stream.
     *
     * @param                                                                                          $path
     * @param                                                                                          $resource
     * @param \RightCapital\FlySystem\Config\Config                                                    $config
     *
     * @return array|false
     */
    public function writeStream($path, $resource, Config $config);

    /**
     * Update a file.
     *
     * @param                                                 $path
     * @param                                                 $contents
     * @param \RightCapital\FlySystem\Config\Config           $config
     *
     * @return array|false
     */
    public function update($path, $contents, Config $config);

    /**
     * Update a file using stream.
     *
     * @param                                                 $path
     * @param                                                 $contents
     * @param \RightCapital\FlySystem\Config\Config           $config
     *
     * @return array|false
     */
    public function updateStream($path, $contents, Config $config);

    /**
     * Rename a file.
     *
     * @param string $path
     * @param string $newpath
     *
     * @return mixed
     */
    public function rename($path, $newpath);

    /**
     * Copy a file.
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     */
    public function copy($path, $newpath);

    /**
     * Delete a file.
     *
     * @param $path
     *
     * @return mixed
     */
    public function delete($path);

    /**
     * Delete a directory.
     *
     * @param string $dirname
     *
     * @return bool
     */
    public function deleteDir($dirname);

    /**
     * Create a directory.
     *
     * @param string                                $dirname
     * @param \RightCapital\FlySystem\Config\Config $config
     *
     * @return array|false
     */
    public function createDir($dirname, Config $config);

    /**
     * Set the visibility for a file.
     *
     * @param string $path
     * @param string $visibility
     *
     * @return array|false
     */
    public function setVisibility($path, $visibility);
}