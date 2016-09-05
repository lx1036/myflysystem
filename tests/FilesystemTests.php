<?php

namespace RightCapital\FlySystem\Test;

use Prophecy\Argument;
use RightCapital\FlySystem\Adapter\AdapterInterface;
use RightCapital\FlySystem\Config\Config;
use RightCapital\FlySystem\Filesystem;

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
        //mock AdapterInterface
        $this->prophecy         = $this->prophesize(AdapterInterface::class);
        $this->adapter          = $this->prophecy->reveal();
        //config object
        $this->filesystemConfig = new Config();
        //filesystem object
        $this->filesystem       = new Filesystem($this->adapter, $this->filesystemConfig);
        $this->config           = Argument::type(Config::class);
    }

    public function testGetAdapter()
    {
        $this->assertEquals($this->adapter, $this->filesystem->getAdapter());
    }

    public function testGetConfig()
    {
        $this->assertInstanceOf(Config::class, $this->filesystem->getConfig());
    }// test constructor

    public function testHas()
    {
        $this->prophecy->has('path.txt')->willReturn(true);
        $this->assertTrue($this->filesystem->has('path.txt'));
    }//test has()

    public function testWrite()
    {
        $path     = 'path.txt';
        $contents = 'contents';
        $this->prophecy->has($path)->willReturn(false);
        $this->prophecy->write($path, $contents, $this->config)->willReturn(compact('path', 'contents'));
        $this->assertTrue($this->filesystem->write($path, $contents));
    }

    public function testWriteWithoutAssert()
    {
        $this->filesystemConfig->set('disable_asserts', true);
        $path     = 'path.txt';
        $contents = 'contents';
        $this->prophecy->write($path, $contents, $this->config)->willReturn(compact('path', 'contents'));
        $this->assertTrue($this->filesystem->write($path, $contents));
    }

    public function testWriteStream()
    {
        $path   = 'path.txt';
        $stream = tmpfile();
        $this->prophecy->has($path)->willReturn(false);
        $this->prophecy->writeStream($path, $stream, $this->config)->willReturn(compact('path'));
        $this->assertTrue($this->filesystem->writeStream($path, $stream));
        fclose($stream);
    }

    public function testUpdate()
    {
        $path     = 'path.txt';
        $contents = 'contents';
        $this->prophecy->has($path)->willReturn(true);
        $this->prophecy->update($path, $contents, $this->config)->willReturn(compact('path', 'contents'));
        $this->assertTrue($this->filesystem->update($path, $contents));
    }

    public function testUpdateStream()
    {
        $path   = 'path.txt';
        $stream = tmpfile();
        $this->prophecy->has($path)->willReturn(true);
        $this->prophecy->updateStream($path, $stream, $this->config)->willReturn(compact('path'));
        $this->assertTrue($this->filesystem->updateStream($path, $stream));
        fclose($stream);
    }

    // File not exists
    public function testPutNew()
    {
        $path     = 'path.txt';
        $contents = 'contents';
        $this->prophecy->has($path)->willReturn(false);
        $this->prophecy->write($path, $contents, $this->config)->willReturn(compact('path', 'contents'));
        $this->assertTrue($this->filesystem->put($path, $contents));
    }

    // File not exists, use stream
    public function testPutNewStream()
    {
        $path   = 'path.txt';
        $stream = tmpfile();
        $this->prophecy->has($path)->willReturn(false);
        $this->prophecy->writeStream($path, $stream, $this->config)->willReturn(compact('path'));
        $this->assertTrue($this->filesystem->putStream($path, $stream));
        fclose($stream);
    }

    // File exists
    public function testPutUpdate()
    {
        $path     = 'path.txt';
        $contents = 'contents';
        $this->prophecy->has($path)->willReturn(true);
        $this->prophecy->update($path, $contents, $this->config)->willReturn(compact('path', 'contents'));
        $this->assertTrue($this->filesystem->put($path, $contents));
    }

    // File exists, use stream
    public function testPutUpdateStream()
    {
        $path   = 'path.txt';
        $stream = tmpfile();
        $this->prophecy->has($path)->willReturn(true);
        $this->prophecy->updateStream($path, $stream, $this->config)->willReturn(compact('path'));
        $this->assertTrue($this->filesystem->putStream($path, $stream));
        fclose($stream);
    }
}