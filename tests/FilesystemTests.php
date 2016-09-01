<?php
/**
 * Created by PhpStorm.
 * User: liuxiang
 * Date: 9/1/16
 * Time: 1:48 PM
 */

namespace RightCapital\FlySystem\FlySystem\Test;

use RightCapital\FlySystem\FlySystem\Adapter\Local;
use RightCapital\FlySystem\FlySystem\Cache\Memory;
use RightCapital\FlySystem\FlySystem\Filesystem;

class FilesystemTests extends \PHPUnit_Framework_TestCase
{
    public function testInstantiable()
    {
        $instance = new Filesystem($adapter = new Local(__DIR__ . '/../resources/'), $cache = new Memory());
    }

    public function filesystemProvider()
    {
        $adapter = new Local(__DIR__ . '/../resources/');
        $filesystem = new Filesystem($adapter, $cache);

        return [
            [$filesystem]
        ];
    }

    /**
     * @dataProvider filesystemProvider
     */
    public function testListContents($filesystem)
    {
        $result = $filesystem->listContents();
        $this->assertInternalType('array', $result);
        $this->assertCount(4, $result);
    }
}