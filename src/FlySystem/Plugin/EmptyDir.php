<?php

namespace RightCapital\FlySystem\FlySystem\Plugin;

class EmptyDir extends AbstractPlugin
{
    /**
     * @return string
     */
    public function getMethod()
    {
        return 'emptyDir';
    }

    /**
     * Empty a directory
     *
     * @param string $dirname
     */
    public function handle($dirname)
    {
        $listing = $this->filesystem->listContents($dirname, false);

        foreach ($listing as $item) {
            if ($item['type'] === 'dir') {
                $this->filesystem->deleteDir($item['path']);
            } else {
                $this->filesystem->delete($item['path']);
            }
        }
    }
}