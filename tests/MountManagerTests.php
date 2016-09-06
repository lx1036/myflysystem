<?php

namespace RightCapital\FlySystem\Test;

use Mockery;
use RightCapital\FlySystem\FilesystemInterface;
use RightCapital\FlySystem\MountManager;

class MountManagerTests extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Mockery\MockInterface|FilesystemInterface
     */
    protected $mockFilesystem;

    public function setUp()
    {
        $this->mockFilesystem = Mockery::mock(FilesystemInterface::class);
    }

    /**
     * Cover mountFilesystems and mountFilesystem method.
     */
    public function testConstructorInjection()
    {
        $manager = new MountManager([
            'prefix' => $this->mockFilesystem,
        ]);
        $this->assertEquals($this->mockFilesystem, $manager->getFilesystem('prefix'));
    }

    /**
     * @expectedException \LogicException
     */
    public function testUndefinedFilesystem()
    {
        (new MountManager())->getFilesystem('prefix');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidPrefix()
    {
        (new MountManager())->mountFilesystem(false, $this->mockFilesystem);
    }

    public function invalidCallProvider()
    {
        return [
            [[], 'LogicException'],
            [[false], 'InvalidArgumentException'],
            [['path/without/protocol'], 'InvalidArgumentException'],
        ];
    }

    /**
     * @dataProvider invalidCallProvider
     */
    public function testInvalidArguments($argument, $exception)
    {
        $this->setExpectedException($exception);
        (new MountManager())->filterPrefix($argument);
    }

    public function testCallMagicMethod()
    {
        $this->mockFilesystem->shouldReceive('onceMethodCall')->once()->andReturn('a result');
        $manager = new MountManager();
        $manager->mountFilesystem('S3', $this->mockFilesystem);
        $this->assertEquals($manager->onceMethodCall('S3://file.txt'), 'a result');
    }

    public function testCopyBetweenFilesystems()
    {
        $manager = new MountManager();
        $fs1     = Mockery::mock(FilesystemInterface::class);
        $fs2     = Mockery::mock(FilesystemInterface::class);
        $manager->mountFilesystem('fs1', $fs1);
        $manager->mountFilesystem('fs2', $fs2);

        $filename = 'test.txt';
        $buffer   = tmpfile();
        $fs1->shouldReceive('readStream')->once()->with($filename)->andReturn($buffer);
        $fs2->shouldReceive('writeStream')->once()->with($filename, $buffer, [])->andReturn(true);
        $response = $manager->copy("fs1://{$filename}", "fs2://{$filename}");
        $this->assertTrue($response);

        // test failed status
        $fs1->shouldReceive('readStream')->once()->with($filename)->andReturn(false);
        $response = $manager->copy("fs1://{$filename}", "fs2://{$filename}");
        $this->assertFalse($response);

        $buffer = tmpfile();
        $fs1->shouldReceive('readStream')->once()->with($filename)->andReturn($buffer);
        $fs2->shouldReceive('writeStream')->once()->with($filename, $buffer, [])->andReturn(false);
        $response = $manager->copy("fs1://{$filename}", "fs2://{$filename}");
        $this->assertFalse($response);
    }

    public function testMoveBetweenFilesystems()
    {
        // Partial Mock
        /**
         * @var MountManager $manager
         */
        $manager = Mockery::mock(MountManager::class)->makePartial();
        $fs1     = Mockery::mock(FilesystemInterface::class);
        $fs2     = Mockery::mock(FilesystemInterface::class);
        $manager->mountFilesystem('fs1', $fs1);
        $manager->mountFilesystem('fs2', $fs2);

        $filename = 'test.txt';
        $buffer   = tmpfile();
        $fs1->shouldReceive('readStream')->once()->with($filename)->andReturn($buffer);
        $fs2->shouldReceive('writeStream')->once()->with($filename, $buffer, [])->andReturn(false);
        $response = $manager->move("fs1://{$filename}", "fs2://{$filename}");
        $this->assertFalse($response);

        $manager->shouldReceive('copy')->with("fs1://{$filename}", "fs2://{$filename}", [])->andReturn(true);
        $manager->shouldReceive('delete')->with("fs1://{$filename}")->andReturn(true);
        $response = $manager->move("fs1://{$filename}", "fs2://{$filename}");
        $this->assertTrue($response);
    }

    public function mockFilesystem()
    {
        $mock = Mockery::mock(FilesystemInterface::class);
        $mock->shouldReceive('listContents')->andReturn([
            ['path' => 'path.txt', 'type' => 'file'],
            ['path' => 'dirname/path.txt', 'type' => 'file'],
        ]);

        return $mock;
    }

    public function testListContents()
    {
        $fs1 = $this->mockFilesystem();
        $fs2 = $this->mockFilesystem();

        $mountManager = new MountManager();
        $mountManager->mountFilesystem('local', $fs1);
        $mountManager->mountFilesystem('huge', $fs2);

        $results = $mountManager->listContents("local://tests/files");

        foreach ($results as $result) {
            $this->assertArrayHasKey('filesystem', $result);
            $this->assertEquals($result['filesystem'], 'local');
        }

        $results = $mountManager->listContents("huge://tests/files");

        foreach ($results as $result) {
            $this->assertArrayHasKey('filesystem', $result);
            $this->assertEquals($result['filesystem'], 'huge');
        }
    }

    public function testListWith()
    {
        $manager  = new MountManager();
        $response = [
            'path'      => 'file.txt',
            'timestamp' => time(),
        ];
        $this->mockFilesystem->shouldReceive('listWith')
            ->with(['timestamp'], 'file.txt', false)
            ->once()
            ->andReturn($response);
        $manager->mountFilesystem('S3', $this->mockFilesystem);
        $this->assertEquals($response, $manager->listWith(['timestamp'], 'S3://file.txt', false));
    }

    public function provideMountSchemas()
    {
        return [['with.dot'], ['with-dash'], ['with+plus'], ['with:colon']];
    }

    /**
     * @dataProvider provideMountSchemas
     *
     * @param $schema
     */
    public function testMountSchemaType($schema)
    {
        $manager = new MountManager();
        $this->mockFilesystem->shouldReceive('onceMethodCall')
            ->once()
            ->andReturn('a result');
        $manager->mountFilesystem($schema, $this->mockFilesystem);
        $this->assertEquals($manager->onceMethodCall($schema . '://file.txt'), 'a result');
    }
}