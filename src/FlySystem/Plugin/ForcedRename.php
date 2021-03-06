<?php

namespace RightCapital\FlySystem\Plugin;

use RightCapital\FlySystem\Exception\FileNotFoundException;

class ForcedRename extends AbstractPlugin
{

    /**
     * Get the method name
     *
     * @return string
     */
    public function getMethod()
    {
        return 'forceRename';
    }

    /**
     * Renames a file, overwriting the destination if it exists.
     *
     * @param string $path    Path to the existing file.
     * @param string $newpath The new path of the file.
     *
     * @throws FileNotFoundException Thrown if $path does not exist.
     *
     * @return bool True on success, false on failure.
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
            return $this->filesystem->rename($path, $newpath);
        }

        return false;
    }
}