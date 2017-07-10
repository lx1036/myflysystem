<?php

namespace RightCapital\FlySystem\Support;

use RightCapital\FlySystem\Config\Config;

class Util
{

    public static function pathinfo($path)
    {
        $pathinfo = pathinfo($path) + compact('path');

        if ($pathinfo['dirname'] === '.') {
            $pathinfo['dirname'] = '';
        }

        return $pathinfo;
    }

    public static function normalizePrefix($realpath, $separator)
    {
        return ltrim($realpath, $separator);
    }

    public static function map($object, array $map)
    {
        $result = [];

        foreach ($map as $from => $to) {
            if (!isset($object[$from])) {
                continue;
            }

            $result[$to] = $object[$from];
        }

        return $result;
    }

    /**
     * Ensure the input should be Config::class
     * 
     * @param $config
     *
     * @return \RightCapital\FlySystem\Config\Config
     */
    public static function ensureConfig($config)
    {
        if ($config === null) {
            return new Config();
        }

        if ($config instanceof Config) {
            return $config;
        }

        if (is_array($config)) {
            return new Config($config);
        }

        throw new \LogicException('A config should either be an array or a Config object.');
    }

    /**
     * Normalize path.
     *
     * @param string $path
     *
     * @return string
     */
    public static function normalizePath($path)
    {
        // Remove any kind of funky unicode whitespace
        $normalized = preg_replace('#\p{C}+|^\./#u', '', $path);
        $normalized = static::normalizeRelativePath($normalized);

        if (preg_match('#/\.{2}|^\.{2}/|^\.{2}$#', $normalized)) {
            throw new \LogicException(
                'Path is outside of the defined root, path: [' . $path . '], resolved: [' . $normalized . ']'
            );
        }

        $normalized = preg_replace('#\\\{2,}#', '\\', trim($normalized, '\\'));
        $normalized = preg_replace('#/{2,}#', '/', trim($normalized, '/'));

        return $normalized;
    }

    /**
     * Normalize relative directories in a path.
     *
     * @param string $path
     *
     * @return string
     */
    private static function normalizeRelativePath($path)
    {
        // Path remove self referring paths ("/./").
        $path = preg_replace('#/\.(?=/)|^\./|(/|^)\./?$#', '', $path);

        // Regex for resolving relative paths
        $regex = '#/*[^/\.]+/\.\.#Uu';

        while (preg_match($regex, $path)) {
            $path = preg_replace($regex, '', $path);
        }

        return $path;
    }

    public static function rewindStream($resource)
    {
        if (ftell($resource) !== 0 && static::isSeekableStream($resource)) {
            rewind($resource);
        }
    }

    public static function isSeekableStream($resource)
    {
        $metadata = stream_get_meta_data($resource);

        //seekable (bool) - 是否可以在当前流中定位
        return $metadata['seekable'];
    }

    public static function guessMimeType($path, $contents)
    {
    }

    /**
     * Get a normalized dirname from a path.
     *
     * @param string $path
     *
     * @return string dirname
     */
    public static function dirname($path)
    {
        return static::normalizeDirname(dirname($path));
    }

    /**
     * Normalize a dirname return value.
     *
     * @param string $dirname
     *
     * @return string normalized dirname
     */
    public static function normalizeDirname($dirname)
    {
        return $dirname === '.' ? '' : $dirname;
    }
}