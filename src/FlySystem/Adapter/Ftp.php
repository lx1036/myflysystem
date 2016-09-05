<?php

namespace RightCapital\FlySystem\Adapter;

use RightCapital\FlySystem\Config\Config;

class Ftp extends AbstractFtpAdapter
{

    /**
     * Check if a connection is alive
     */
    public function isConnected()
    {
        // TODO: Implement isConnected() method.
    }

    /**
     * Close a connection
     */
    public function disconnect()
    {
        // TODO: Implement disconnect() method.
    }

    /**
     * Establish a connection
     */
    public function connect()
    {
        // TODO: Implement connect() method.
    }

    protected function listDirectoryContents($directory = '', $recursive = false)
    {
        // TODO: Implement listDirectoryContents() method.
    }

    /**
     * Write a new file.
     *
     * @param                                                 $path
     * @param                                                 $contents
     * @param \RightCapital\FlySystem\Config\Config           $config
     *
     * @return array|false
     */
    public function write($path, $contents, Config $config)
    {
        // TODO: Implement write() method.
    }

    /**
     * Write a new file using stream.
     *
     * @param                                                                                          $path
     * @param                                                                                          $resource
     * @param \RightCapital\FlySystem\Config\Config                                                    $config
     *
     * @return array|false
     */
    public function writeStream($path, $resource, Config $config)
    {
        // TODO: Implement writeStream() method.
    }

    /**
     * Update a file.
     *
     * @param                                                 $path
     * @param                                                 $contents
     * @param \RightCapital\FlySystem\Config\Config           $config
     *
     * @return array|false
     */
    public function update($path, $contents, Config $config)
    {
        // TODO: Implement update() method.
    }

    /**
     * Update a file using stream.
     *
     * @param                                                 $path
     * @param                                                 $contents
     * @param \RightCapital\FlySystem\Config\Config           $config
     *
     * @return array|false
     */
    public function updateStream($path, $contents, Config $config)
    {
        // TODO: Implement updateStream() method.
    }

    /**
     * Rename a file.
     *
     * @param string $path
     * @param string $newpath
     *
     * @return mixed
     */
    public function rename($path, $newpath)
    {
        // TODO: Implement rename() method.
    }

    /**
     * Copy a file.
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     */
    public function copy($path, $newpath)
    {
        // TODO: Implement copy() method.
    }

    /**
     * Delete a file.
     *
     * @param $path
     *
     * @return mixed
     */
    public function delete($path)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Delete a directory.
     *
     * @param string $dirname
     *
     * @return bool
     */
    public function deleteDir($dirname)
    {
        // TODO: Implement deleteDir() method.
    }

    /**
     * Create a directory.
     *
     * @param string                                $dirname
     * @param \RightCapital\FlySystem\Config\Config $config
     *
     * @return array|false
     */
    public function createDir($dirname, Config $config)
    {
        // TODO: Implement createDir() method.
    }

    /**
     * Set the visibility for a file.
     *
     * @param string $path
     * @param string $visibility
     *
     * @return array|false
     */
    public function setVisibility($path, $visibility)
    {
        // TODO: Implement setVisibility() method.
    }

    /**
     * Check if the file exists.
     *
     * @param \RightCapital\FlySystem\string $path
     *
     * @return array|bool|null
     */
    public function has($path)
    {
        // TODO: Implement has() method.
    }

    /**
     * Read a file.
     *
     * @param \RightCapital\FlySystem\string $path
     *
     * @return array|false
     */
    public function read($path)
    {
        // TODO: Implement read() method.
    }

    /**
     * Read a file as a stream.
     *
     * @param \RightCapital\FlySystem\string $path
     *
     * @return array|false
     */
    public function readStream($path)
    {
        // TODO: Implement readStream() method.
    }

    /**
     * Get all the meta data of a file or directory.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getMetadata($path)
    {
        // TODO: Implement getMetadata() method.
    }

    /**
     * Get the file size.
     *
     * @param string $path
     *
     * @return int
     */
    public function getSize($path)
    {
        // TODO: Implement getSize() method.
    }

    /**
     * Get the mimetype of a file.
     *
     * @param \RightCapital\FlySystem\string $path
     *
     * @return mixed
     */
    public function getMimetype($path)
    {
        // TODO: Implement getMimetype() method.
    }

    /**
     * Get the timestamp of a file.
     *
     * @param \RightCapital\FlySystem\string $path
     *
     * @return mixed
     */
    public function getTimestamp($path)
    {
        // TODO: Implement getTimestamp() method.
    }

    /**
     * Get the visibility of a file.
     *
     * @param \RightCapital\FlySystem\string $path
     *
     * @return array|false
     */
    public function getVisibility($path)
    {
        // TODO: Implement getVisibility() method.
    }
}