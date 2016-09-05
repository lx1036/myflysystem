<?php
/**
 * Created by PhpStorm.
 * User: liuxiang
 * Date: 9/1/16
 * Time: 1:48 PM
 */

namespace RightCapital\FlySystem\FlySystem\Test;

use Prophecy\Argument;
use RightCapital\FlySystem\FlySystem\Adapter\Local;
use RightCapital\FlySystem\FlySystem\AdapterInterface;
use RightCapital\FlySystem\FlySystem\Cache\Memory;
use RightCapital\FlySystem\FlySystem\Config\Config;
use RightCapital\FlySystem\FlySystem\Filesystem;

class FilesystemTests extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Prophecy\Prophecy\ObjectProphecy
     */
    protected $prophecy;
    /**
     * @var AdapterInterface
     */
    protected $adapter;
    /**
     * @var Config
     */
    protected $filesystemConfig;
    /**
     * @var Filesystem
     */
    protected $filesystem;
    /**
     * @var Argument\Token\TypeToken
     */
    protected $config;

    /**
     * @before
     */
    public function setupAdapter()
    {
        $this->prophecy = $this->prophesize(AdapterInterface::class);
        $this->adapter = $this->prophecy->reveal();
        $this->filesystemConfig = new Config();
        $this->filesystem = new Filesystem($this->adapter, $this->filesystemConfig);
        $this->config = Argument::type(Config::class);
    }
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