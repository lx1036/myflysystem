<?php

namespace RightCapital\FlySystem\Test;

use Mockery;
use RightCapital\FlySystem\Adapter\AdapterInterface;
use RightCapital\FlySystem\Filesystem;

class FilesystemStreamTests extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Mockery\MockInterface $adapter
     */
    protected $adapter;
    /**
     * @var Filesystem $filesystem
     */
    protected $filesystem;

    public function setUp()
    {
        $this->filesystem = new Filesystem($this->adapter = Mockery::mock(AdapterInterface::class));
    }

    public function testWriteStream()
    {
        $this->adapter->shouldReceive('has')->andReturn(false);
        $this->adapter->shouldReceive('writeStream')->andReturn(['path' => 'file.txt'], false);
        $this->assertTrue($this->filesystem->writeStream('file.txt', tmpfile()));
        $this->assertFalse($this->filesystem->writeStream('file.txt', tmpfile()));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWriteStreamFail()
    {
        $this->adapter->shouldReceive('has')->andReturn(false);
        $this->filesystem->writeStream('file.txt', 'not a resource');
    }

    public function testUpdateStream()
    {
        $this->adapter->shouldReceive('has')->andReturn(true);
        $this->adapter->shouldReceive('updateStream')->andReturn(['path' => 'file.txt'], false);
        $this->assertTrue($this->filesystem->updateStream('file.txt', tmpfile()));
        $this->assertFalse($this->filesystem->updateStream('file.txt', tmpfile()));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUpdateStreamFail()
    {
        $this->adapter->shouldReceive('has')->andReturn(false);
        $this->filesystem->updateStream('file.txt', 'not a resource');
    }

    public function testReadStream()
    {
        $this->adapter->shouldReceive('has')->andReturn(true);
        $stream = tmpfile();
        $this->adapter->shouldReceive('readStream')->times(3)->andReturn(['stream' => $stream], false, false);
        $this->assertInternalType('resource', $this->filesystem->readStream('file.txt'));
        $this->assertFalse($this->filesystem->readStream('other.txt'));
        fclose($stream);
        $this->assertFalse($this->filesystem->readStream('other.txt'));
    }
}