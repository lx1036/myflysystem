<?php

namespace RightCapital\FlySystem\Adapter;

abstract class AbstractAdapter implements AdapterInterface
{
    /**
     * @var string $pathPrefix path prefix
     */
    protected $pathPrefix;
    /**
     * @var string
     */
    protected $pathSeperator = '/';

    public function emulateDirectories($listing)
    {
        $directories = array();

        foreach ($listing as $object)
        {
            if (!empty($object['dirname']))
                $directories[] = $object['dirname'];
        }

        $directories = array_unique($directories);

        foreach ($directories as $directory)
        {
            $directory = pathinfo($directory) + ['path' => $directory, 'type' => 'dir'];

            if ($directory['dirname'] === '.') {
                $directory['dirname'] = '';
            }

            $listing[] = $directory;
        }

        return $listing;
    }

    /**
     * @return string
     */
    public function getPathPrefix()
    {
        return $this->pathPrefix;
    }

    /**
     * @param string $pathPrefix
     */
    public function setPathPrefix($pathPrefix)
    {
        $isEmpty = empty($pathPrefix);

        if (!$isEmpty) {
            $pathPrefix = rtrim($pathPrefix, $this->pathSeperator) . $this->pathSeperator;
        }

        $this->pathPrefix = $isEmpty ? null : $pathPrefix;
    }

    /**
     * Prefix a path
     *
     * @param $path
     *
     * @return string
     */
    public function applyPathPrefix($path)
    {
        $path = ltrim($path, '\\/');

        if (strlen($path) === 0) {
            return $this->getPathPrefix() ?: '';
        }

        if ($prefix = $this->getPathPrefix()) {
            $path = $prefix . $path;
        }

        return $path;
    }

    /**
     * Remove a path prefix
     * @param $path
     *
     * @return string
     */
    public function removePathPrefix($path)
    {
        $pathPrefix = $this->getPathPrefix();
        
        if ($pathPrefix === null) {
            return $path;
        }

        return substr($path, strlen($pathPrefix));
    }
}
