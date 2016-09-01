<?php
/**
 * Created by PhpStorm.
 * User: liuxiang
 * Date: 9/1/16
 * Time: 1:04 PM
 */

namespace RightCapital\FlySystem\FlySystem;

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

    public static function contentMimetype($contents)
    {
        $finfo = new Finfo(FILEINFO_MIME_TYPE);

        return $finfo->buffer($contents);
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
}