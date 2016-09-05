<?php

namespace RightCapital\FlySystem\FlySystem\Plugin;

use RightCapital\FlySystem\FlySystem\Exception\FileNotFoundException;

class ForcedCopy extends AbstractPlugin
{

    /**
     * Get the method name
     *
     * @return string
     */
    public function getMethod()
    {
        return 'forceCopy';
    }

    /**
     * Copies a file, overwriting any existing files.
     *
     * @param string $path
     * @param string $newpath
     *
     * @throws FileNotFoundException
     *
     * @return bool
     */
    public function handle($path, $newpath)
    {
        try {
            $deleted = $this->filesystem->delete($newpath);
        } catch (FileNotFoundException $e) {
            // The destination path does not exist. That's ok.
            $deleted = true;
        }

        if ($deleted) {
            return $this->filesystem->copy($path, $newpath);
        }

        return false;
    }
}