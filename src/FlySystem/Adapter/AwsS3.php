<?php

namespace RightCapital\FlySystem\Adapter;

use Aws\S3\S3Client;
use RightCapital\FlySystem\Config\Config;
use RightCapital\FlySystem\Support\Util;

class AwsS3 extends AbstractAdapter
{
    /**
     * @var \Aws\S3\S3Client
     */
    private $s3Client;
    /**
     * @var
     */
    private $bucket;
    /**
     * @var null
     */
    private $prefix;
    /**
     * @var array
     */
    private $options;

    protected static $resultMap = [
        'Body'          => 'contents',
        'ContentLength' => 'size',
        'ContentType'   => 'mimetype',
        'Size'          => 'size',
    ];

    public function __construct(S3Client $s3Client, $bucket, $prefix = null, array $options = [])
    {

        $this->s3Client = $s3Client;
        $this->bucket   = $bucket;
        $this->prefix   = $prefix;
        $this->options  = $options;
    }

    public function write($path, $contents)
    {
        $options = $this->getOptions($path, [
            'Body'          => $contents,
            'ContentType'   => Util::contentMimetype($contents),
            'ContentLenght' => Util::contentSize($contents),
        ]);

        $this->s3Client->putObject($options);

        return $this->normalizeObject($options);
    }

    public function update($path, $contents)
    {
        return $this->write($path, $contents);
    }

    public function rename($path, $newpath)
    {
        $options = $this->getOptions($newpath, [
            'Bucket'     => $this->bucket,
            'CopySource' => $this->bucket . '/' . $this->prefix($path),
        ]);

        $result = $this->s3Client->copyObject($options)->getAll();
        $result = $this->normalizeObject($result, $newpath);
        $this->delete($path);

        return $result;
    }

    public function delete($path)
    {
        $options = $this->getOptions($path);

        return $this->s3Client->deleteObject($options);
    }

    public function deleteDir($dirname)
    {
        $this->s3Client->deleteMatchingObjects($this->bucket, $this->prefix($dirname));
    }

    public function createDir($path)
    {
        return compact('path');
    }

    public function has($path)
    {
        return $this->s3Client->doesObjectExist($this->bucket, $this->prefix($path));
    }

    public function read($path)
    {
        $options = $this->getOptions($path);
        $result = $this->s3Client->getObject($options);

        return $this->normalizeObject($result->getAll());
    }

    public function listContents()
    {
        $result = $this->s3Client->listObjects([
            'Bucket' => $this->bucket,
        ])->getAll(['Contents']);

        if (!isset($result['Contents'])) {
            return [];
        }

        $result = array_map([$this, 'normalizeObject'], $result['Contents']);

        return $this->emulateDirectories($result);
    }

    public function getMetadata($path)
    {
        $options = $this->getOptions($path);
        $result = $this->s3Client->headObject($options);

        return $this->normalizeObject($result->getAll(), $path);
    }

    public function getMimetype($path)
    {
        return $this->getMetadata($path);
    }

    private function getOptions($path, array $options = [])
    {
        $options['Key']    = $this->prefix($path);
        $options['Bucket'] = $this->bucket;

        return array_merge($this->options, $options);
    }

    private function normalizeObject($object, $path = null)
    {
        $result = ['path' => $path ?: $object['Key']];

        if (isset($object['LastModified'])) {
            $object['timestamp'] = strtotime($object['LastModified']);
        }

        return array_merge($result, Util::map($object, static::$resultMap), ['type' => 'file']);
    }

    private function prefix($path)
    {
        if (!$this->prefix) {
            return $path;
        }

        return $this->prefix . '/' . $path;
    }

    /**
     * Write a new file using stream.
     *
     * @param                                                                                          $path
     * @param                                                                                          $resource
     * @param \RightCapital\FlySystem\Config\Config                                                    $config
     *
     * @return array|false
     */
    public function writeStream($path, $resource, Config $config)
    {
        // TODO: Implement writeStream() method.
    }

    /**
     * Update a file using stream.
     *
     * @param                                                 $path
     * @param                                                 $contents
     * @param \RightCapital\FlySystem\Config\Config           $config
     *
     * @return array|false
     */
    public function updateStream($path, $contents, Config $config)
    {
        // TODO: Implement updateStream() method.
    }

    /**
     * Copy a file.
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     */
    public function copy($path, $newpath)
    {
        // TODO: Implement copy() method.
    }

    /**
     * Set the visibility for a file.
     *
     * @param string $path
     * @param string $visibility
     *
     * @return array|false
     */
    public function setVisibility($path, $visibility)
    {
        // TODO: Implement setVisibility() method.
    }

    /**
     * Read a file as a stream.
     *
     * @param \RightCapital\FlySystem\string $path
     *
     * @return array|false
     */
    public function readStream($path)
    {
        // TODO: Implement readStream() method.
    }

    /**
     * Get the file size.
     *
     * @param string $path
     *
     * @return int
     */
    public function getSize($path)
    {
        // TODO: Implement getSize() method.
    }

    /**
     * Get the timestamp of a file.
     *
     * @param \RightCapital\FlySystem\string $path
     *
     * @return mixed
     */
    public function getTimestamp($path)
    {
        // TODO: Implement getTimestamp() method.
    }

    /**
     * Get the visibility of a file.
     *
     * @param \RightCapital\FlySystem\string $path
     *
     * @return array|false
     */
    public function getVisibility($path)
    {
        // TODO: Implement getVisibility() method.
    }

    /**
     * is triggered when invoking inaccessible methods in an object context.
     *
     * @param $name      string
     * @param $arguments array
     *
     * @return mixed
     * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.methods
     */
    function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
    }
}