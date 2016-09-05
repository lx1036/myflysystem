<?php

namespace RightCapital\FlySystem\Plugin;

class GetWithMetadata extends AbstractPlugin
{

    /**
     * Get the method name
     *
     * @return string
     */
    public function getMethod()
    {
        return 'getWithMetadata';
    }

    /**
     * Get metadata for an object with required metadata.
     *
     * @param string $path     path to file
     * @param array  $metadata metadata keys
     *
     * @throws \InvalidArgumentException
     *
     * @return array|bool metadata
     */
    public function handle($path, array $metadata)
    {
        if (! $object = $this->filesystem->getMetadata($path)) {
            return false;
        }

        $keys = array_diff($metadata, array_keys($object));

        foreach ($keys as $key) {
            if (!method_exists($this->filesystem, $method = 'get' . ucfirst($key))) {
                throw new \InvalidArgumentException('Could not fetch metadata: ' . $key);
            }

            $object[$key] = $this->filesystem->{$method}($path);
        }

        return $object;
    }
}