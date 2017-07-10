<?php

namespace RightCapital\FlySystem\Plugin;

class ListWith extends AbstractPlugin
{

    /**
     * Get the method name
     *
     * @return string
     */
    public function getMethod()
    {
        return 'listWith';
    }

    public function handle(array $keys = [], $directory = '', $recursive = false)
    {
        $contents = $this->filesystem->listContents($directory, $recursive);

        foreach ($contents as $index => $object) {
            if ($object['type'] === 'file') {
                $missingKeys = array_diff($keys, array_keys($object));
                $contents[$index] = array_reduce($missingKeys, [$this, 'getMetadataByName'], $object);
            }
        }

        return $contents;
    }

    protected function getMetadataByName(array $object, $key)
    {
        $method = 'get' . ucfirst($key);

        if (!method_exists($this->filesystem, $method)) {
            throw new \InvalidArgumentException('Could not get metadata for key: ' . $key);
        }

        $object[$key] = $this->filesystem->{$method}($object['path']);

        return $object;
    }
}