<?php
/**
 * Created by PhpStorm.
 * User: liuxiang
 * Date: 9/1/16
 * Time: 1:40 PM
 */

namespace RightCapital\FlySystem\FlySystem\Adapter;

use Aws\S3\S3Client;
use RightCapital\FlySystem\FlySystem\Util;

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
}