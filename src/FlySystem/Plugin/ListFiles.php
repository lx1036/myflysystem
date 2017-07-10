<?php

namespace RightCapital\FlySystem\Plugin;

class ListFiles extends AbstractPlugin
{

    /**
     * Get the method name
     *
     * @return string
     */
    public function getMethod()
    {
        return 'listFiles';
    }

    /**
     * Only list all files in the directory.
     *
     * @param string $directory
     * @param bool   $recursive
     *
     * @return array
     */
    public function handle($directory = '', $recursive = false)
    {
        $contents = $this->filesystem->listContents($directory, $recursive);

        return array_values(array_filter($contents, function ($object) {
            return $object['type'] === 'file';
        }));
    }
}