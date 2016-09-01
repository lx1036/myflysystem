<?php
/**
 * Created by PhpStorm.
 * User: liuxiang
 * Date: 9/1/16
 * Time: 1:40 PM
 */

namespace RightCapital\FlySystem\FlySystem\Adapter;

use RightCapital\FlySystem\FlySystem\AdapterInterface;

abstract class AbstractAdapter implements AdapterInterface
{
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

}